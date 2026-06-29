<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $base = Expense::where('company_id', $companyId)
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($w) => $w->where('description', 'like', "%{$s}%")->orWhere('supplier', 'like', "%{$s}%"));
            });

        $stats = [
            'totaal' => (int) (clone $base)->sum('amount_incl'),
            'voorbelasting' => (int) (clone $base)->sum('vat_amount'),
            'aantal' => (clone $base)->count(),
        ];

        $expenses = (clone $base)->with('car')->orderByDesc('date')->orderByDesc('id')->paginate(30)->withQueryString();
        $cars = Car::where('company_id', $companyId)->orderBy('merk')->get();

        return view('company.expenses.index', compact('expenses', 'stats', 'cars'));
    }

    public function store(Request $request)
    {
        $data = $this->validateExpense($request);
        $expense = new Expense($this->amounts($data));
        $expense->company_id = $request->user()->company_id;
        $expense->car_id = $this->resolveCar($request, $request->user()->company_id);
        $this->attachFile($request, $expense);
        $expense->save();

        return back()->with('success', 'Kostenpost toegevoegd.');
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($request, $expense);
        $data = $this->validateExpense($request);
        $expense->fill($this->amounts($data));
        $expense->car_id = $this->resolveCar($request, $request->user()->company_id);
        $this->attachFile($request, $expense);
        $expense->save();

        return back()->with('success', 'Kostenpost bijgewerkt.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $this->authorizeExpense($request, $expense);
        if ($expense->attachment_path) {
            Storage::disk('local')->delete($expense->attachment_path);
        }
        $expense->delete();

        return back()->with('success', 'Kostenpost verwijderd.');
    }

    /** Stream the private receipt/attachment after an ownership check. */
    public function attachment(Request $request, Expense $expense)
    {
        $this->authorizeExpense($request, $expense);
        abort_unless($expense->attachment_path && Storage::disk('local')->exists($expense->attachment_path), 404);

        return Storage::disk('local')->download($expense->attachment_path, $expense->attachment_name ?: 'bijlage');
    }

    // Helpers ---------------------------------------------------------------

    private function authorizeExpense(Request $request, Expense $expense): void
    {
        abort_unless($expense->company_id === $request->user()->company_id, 403);
    }

    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'date' => 'required|date',
            'supplier' => 'nullable|string|max:150',
            'description' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'amount' => 'required|numeric|min:0',
            'vat_rate' => 'required|in:0,9,21',
            'car_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);
    }

    /** The amount is entered including VAT; split it into excl + VAT. */
    private function amounts(array $data): array
    {
        $incl = (int) round(((float) str_replace(',', '.', (string) $data['amount'])) * 100);
        $rate = (int) $data['vat_rate'];
        $excl = (int) round($incl / (1 + $rate / 100));

        return [
            'date' => $data['date'],
            'supplier' => $data['supplier'] ?? null,
            'description' => $data['description'],
            'category' => $data['category'],
            'vat_rate' => $rate,
            'amount_incl' => $incl,
            'amount_excl' => $excl,
            'vat_amount' => $incl - $excl,
            'notes' => $data['notes'] ?? null,
        ];
    }

    private function resolveCar(Request $request, int $companyId): ?int
    {
        if (! $request->filled('car_id')) {
            return null;
        }

        return Car::where('company_id', $companyId)->where('id', $request->car_id)->value('id');
    }

    private function attachFile(Request $request, Expense $expense): void
    {
        if (! $request->hasFile('attachment')) {
            return;
        }

        if ($expense->attachment_path) {
            Storage::disk('local')->delete($expense->attachment_path);
        }

        $file = $request->file('attachment');
        $expense->attachment_path = $file->store('expenses/' . $request->user()->company_id, 'local');
        $expense->attachment_name = $file->getClientOriginalName();
    }
}
