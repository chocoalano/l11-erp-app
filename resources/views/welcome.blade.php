<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>E-SAS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute -left-0 top-0 max-w-[80px]"
            src="{{ asset('images/svg/bg.svg') }}" />
            {{-- {{ asset('images/svg/bg.svg') }} --}}
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#079246] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <main class="mt-6">
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">

                        <a href="{{ route('filament.hr.auth.login') }}"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#079246] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#079246]">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#079246]/10 sm:size-16">
                                <img src="{{ asset('images/svg/intro-hrd.svg') }}" alt="intro-hrd">
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">HRIS (Human Resource
                                    Information System)</h2>

                                <p class="mt-4 text-sm/relaxed">
                                    HRIS (Human Resource Information System) is a system used to manage data and
                                    information related to human resources. The description of an HRIS system will
                                    include the various components and main purposes of the system, such as
                                    <strong class="font-semibold text-black dark:text-white">
                                        Employee Data Management
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Employee Performance Management
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Payroll and Benefit Management
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Recruitment and Selection
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Employee Development
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Absence and Leave Management
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Reporting and Analysis
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Legal Compliance and Regulation
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Employee Self-Service
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">
                                        Integration with Other Systems
                                    </strong>.
                                </p>
                            </div>

                            <svg class="size-6 shrink-0 self-center stroke-[#079246]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </a>

                        <a href="{{ route('filament.it.auth.login') }}"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#079246] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#079246]">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#079246]/10 sm:size-16">
                                <img src="{{ asset('images/svg/intro-it.svg') }}" alt="intro-hrd">
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">ITS (Information Technology
                                    System)</h2>

                                <p class="mt-4 text-sm/relaxed">
                                    ITS (Information Technology System) includes various components and functions that
                                    aim to manage and support the information technology infrastructure. Here are the
                                    main points that can be included in the ITS system module, <strong
                                        class="font-semibold text-black dark:text-white">Information Technology
                                        Infrastructure
                                    </strong>, <strong class="font-semibold text-black dark:text-white">Information
                                        Security, Database
                                        Management
                                    </strong>, <strong class="font-semibold text-black dark:text-white">Software
                                        Development
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">Technical Support
                                    </strong>,
                                    <strong class="font-semibold text-black dark:text-white">IT Service
                                        Management
                                    </strong>, <strong class="font-semibold text-black dark:text-white">IT
                                        Policy and Compliance
                                    </strong>, <strong class="font-semibold text-black dark:text-white">Technology
                                        Infrastructure
                                        Development
                                    </strong>, <strong class="font-semibold text-black dark:text-white">System
                                        Performance Monitoring
                                        and Evaluation</strong>.
                                </p>
                            </div>

                            <svg class="size-6 shrink-0 self-center stroke-[#079246]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </a>
                        <a href="{{ route('filament.marketing.auth.login') }}"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#079246] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#079246]">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#079246]/10 sm:size-16">
                                <img src="{{ asset('images/svg/intro-marketing.svg') }}" alt="intro-hrd">
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">MIS (Marketing Information
                                    System)</h2>

                                <p class="mt-4 text-sm/relaxed">
                                    Marketing Information System (MIS) is a system designed to collect, store, analyze, and distribute information relevant to marketing management. The goal is to help marketing management make better and faster decisions, and help introduce products or companies to the public (potential customers). The following modules are included, among others, 
                                    <strong class="font-semibold text-black dark:text-white">Contact and customer management</strong>, 
                                    <strong class="font-semibold text-black dark:text-white">Customer services, Report & Analytics</strong>, 
                                    <strong class="font-semibold text-black dark:text-white">partner relationship management</strong>,
                                    <strong class="font-semibold text-black dark:text-white">Management Search Engine Optimization (SEO)</strong>,
                                    <strong class="font-semibold text-black dark:text-white">Website Management</strong>
                                </p>
                            </div>

                            <svg class="size-6 shrink-0 self-center stroke-[#079246]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </a>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    ERP PT. SINERGI ABADI SENTOSA v{{ Illuminate\Foundation\Application::VERSION }} (PHP
                    v{{ PHP_VERSION }})
                </footer>
            </div>
        </div>
    </div>
</body>

</html>
