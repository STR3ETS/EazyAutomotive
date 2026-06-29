<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $year = (int) now()->year;

        $stats = [
            'openstaand' => (int) Invoice::where('company_id', $companyId)
                ->whereIn('status', ['verzonden', 'deels_betaald', 'vervallen'])
                ->sum(\DB::raw('total - amount_paid')),
            'concepten' => Invoice::where('company_id', $companyId)->where('status', 'concept')->count(),
            'omzet_jaar' => (int) Invoice::where('company_id', $companyId)
                ->whereNotIn('status', ['concept', 'geannuleerd'])
                ->where('year', $year)
                ->sum('total'),
            'vervallen' => Invoice::where('company_id', $companyId)->where('status', 'vervallen')->count(),
        ];

        $invoices = Invoice::where('company_id', $companyId)
            ->with('customer')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($w) use ($s) {
                    $w->where('number', 'like', "%{$s}%")
                        ->orWhere('bill_to_name', 'like', "%{$s}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('naam', 'like', "%{$s}%")->orWhere('bedrijfsnaam', 'like', "%{$s}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('company.invoices.index', compact('invoices', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = $request->user()->company_id;
        $customers = Customer::where('company_id', $companyId)->orderBy('naam')->get();
        $cars = Car::where('company_id', $companyId)->orderBy('merk')->get();

        $prefill = null;
        if ($request->filled('car_id')) {
            $car = Car::where('company_id', $companyId)->find($request->car_id);
            if ($car) {
                $prefill = [
                    'car_id' => $car->id,
                    'description' => trim($car->display_title . ($car->kenteken ? ' (' . $car->kenteken . ')' : '')),
                    'unit_price' => $car->prijs ? round($car->prijs / 100, 2) : 0,
                ];
            }
        }

        return view('company.invoices.create', [
            'invoice' => null,
            'customers' => $customers,
            'cars' => $cars,
            'prefill' => $prefill,
            'defaultDate' => now()->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $companyId = $request->user()->company_id;
        $data = $this->validateInvoice($request);

        $customerId = $this->resolveCustomer($request, $companyId);

        $invoice = Invoice::create([
            'company_id' => $companyId,
            'customer_id' => $customerId,
            'car_id' => $data['car_id'] ?? null,
            'status' => 'concept',
            'vat_scheme' => $data['vat_scheme'],
            'prices_include_vat' => $request->boolean('prices_include_vat'),
            'date' => $data['date'],
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->syncLines($invoice, $data['lines']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Concept-factuur aangemaakt.');
    }

    public function show(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);
        $invoice->load(['lines.car', 'customer', 'payments', 'company']);

        return view('company.invoices.show', compact('invoice'));
    }

    public function edit(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);
        abort_unless($invoice->isConcept(), 403, 'Alleen concept-facturen kunnen worden bewerkt.');

        $companyId = $request->user()->company_id;
        $invoice->load('lines');

        return view('company.invoices.create', [
            'invoice' => $invoice,
            'customers' => Customer::where('company_id', $companyId)->orderBy('naam')->get(),
            'cars' => Car::where('company_id', $companyId)->orderBy('merk')->get(),
            'prefill' => null,
            'defaultDate' => $invoice->date->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);
        abort_unless($invoice->isConcept(), 403, 'Alleen concept-facturen kunnen worden bewerkt.');

        $data = $this->validateInvoice($request);
        $customerId = $this->resolveCustomer($request, $request->user()->company_id);

        $invoice->update([
            'customer_id' => $customerId,
            'car_id' => $data['car_id'] ?? null,
            'vat_scheme' => $data['vat_scheme'],
            'prices_include_vat' => $request->boolean('prices_include_vat'),
            'date' => $data['date'],
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice->lines()->delete();
        $this->syncLines($invoice, $data['lines']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Factuur bijgewerkt.');
    }

    /** Finalise a concept invoice: assign the legal number and mark it as sent. */
    public function send(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);

        if (! $invoice->isConcept()) {
            return back()->with('error', 'Deze factuur is al definitief.');
        }
        if ($invoice->lines()->count() === 0) {
            return back()->with('error', 'Voeg eerst minstens een regel toe.');
        }

        $invoice->recalculate();
        $invoice->assignNumber();
        $customer = $invoice->customer;
        $invoice->fill([
            'status' => 'verzonden',
            'sent_at' => now(),
            'due_date' => $invoice->due_date ?: now()->addDays((int) ($invoice->company->invoice_payment_terms ?? 14)),
            'bill_to_name' => $customer?->label,
            'bill_to_address' => $customer?->address_block,
        ])->save();

        return back()->with('success', 'Factuur ' . $invoice->number . ' is definitief gemaakt.');
    }

    public function registerPayment(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);
        abort_if($invoice->isConcept(), 403, 'Maak de factuur eerst definitief.');

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'method' => 'nullable|in:bank,contant,pin',
            'note' => 'nullable|string|max:255',
        ]);

        $invoice->payments()->create([
            'company_id' => $invoice->company_id,
            'date' => $data['date'],
            'amount' => $this->toCents($data['amount']),
            'method' => $data['method'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        $invoice->syncPaymentStatus();

        return back()->with('success', 'Betaling geregistreerd.');
    }

    public function cancel(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);
        $invoice->update(['status' => 'geannuleerd']);

        return back()->with('success', 'Factuur geannuleerd.');
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);
        abort_unless($invoice->isConcept(), 403, 'Alleen concept-facturen kunnen worden verwijderd. Maak een definitieve factuur ongedaan via Annuleren.');

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Concept verwijderd.');
    }

    /** A clean, full-page invoice for printing or saving as PDF from the browser. */
    public function print(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);
        $invoice->load(['lines', 'customer', 'company']);

        return view('company.invoices.print', compact('invoice'));
    }

    // Helpers ---------------------------------------------------------------

    private function authorizeInvoice(Request $request, Invoice $invoice): void
    {
        abort_unless($invoice->company_id === $request->user()->company_id, 403);
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'customer_id' => 'nullable|integer',
            'new_customer_naam' => 'nullable|string|max:150',
            'new_customer_type' => 'nullable|in:particulier,zakelijk',
            'new_customer_email' => 'nullable|email|max:190',
            'new_customer_adres' => 'nullable|string|max:190',
            'new_customer_postcode' => 'nullable|string|max:12',
            'new_customer_plaats' => 'nullable|string|max:120',
            'new_customer_btw' => 'nullable|string|max:20',
            'car_id' => 'nullable|integer',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'vat_scheme' => 'required|in:btw,marge',
            'prices_include_vat' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
            'lines' => 'required|array|min:1',
            'lines.*.description' => 'required|string|max:255',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric',
            'lines.*.vat_rate' => 'required|in:0,9,21',
            'lines.*.purchase_price' => 'nullable|numeric',
            'lines.*.car_id' => 'nullable|integer',
        ]);
    }

    private function resolveCustomer(Request $request, int $companyId): ?int
    {
        if ($request->filled('customer_id')) {
            $customer = Customer::where('company_id', $companyId)->find($request->customer_id);
            if ($customer) {
                return $customer->id;
            }
        }

        if ($request->filled('new_customer_naam')) {
            $customer = Customer::create([
                'company_id' => $companyId,
                'type' => $request->input('new_customer_type', 'particulier'),
                'naam' => $request->new_customer_naam,
                'email' => $request->new_customer_email,
                'adres' => $request->new_customer_adres,
                'postcode' => $request->new_customer_postcode,
                'plaats' => $request->new_customer_plaats,
                'btw_nummer' => $request->new_customer_btw,
            ]);

            return $customer->id;
        }

        return null;
    }

    private function syncLines(Invoice $invoice, array $lines): void
    {
        $companyId = $invoice->company_id;
        $position = 0;

        foreach ($lines as $line) {
            $carId = null;
            if (! empty($line['car_id'])) {
                $carId = Car::where('company_id', $companyId)->where('id', $line['car_id'])->value('id');
            }

            $invoice->lines()->create([
                'car_id' => $carId,
                'description' => $line['description'],
                'quantity' => $line['quantity'],
                'unit_price' => $this->toCents($line['unit_price']),
                'vat_rate' => (int) $line['vat_rate'],
                'purchase_price' => isset($line['purchase_price']) && $line['purchase_price'] !== null && $line['purchase_price'] !== ''
                    ? $this->toCents($line['purchase_price'])
                    : null,
                'position' => $position++,
            ]);
        }

        $invoice->load('lines');
        $invoice->recalculate();
    }

    private function toCents($value): int
    {
        // Number inputs send a dot decimal; also accept a comma decimal just in case.
        return (int) round(((float) str_replace(',', '.', (string) $value)) * 100);
    }
}
