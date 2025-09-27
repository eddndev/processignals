@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-md shadow-sm border-slate-300 text-slate-800 placeholder:text-slate-400 focus:border-green-gold-500 focus:ring-green-gold-500 sm:text-sm/6 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200 dark:placeholder:text-slate-400 dark:focus:border-green-gold-500 dark:focus:ring-green-gold-500']) }}>
