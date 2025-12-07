<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center text-center text-center px-4 py-2 bg-[#262626] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
