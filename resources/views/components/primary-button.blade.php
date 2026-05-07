<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-eazy border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-eazy-dark focus:bg-eazy-dark active:bg-eazy-darker focus:outline-none focus:ring-2 focus:ring-eazy-400 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
