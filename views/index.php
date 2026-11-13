<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink — Organ Donor & Recipient Matching System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        medical: {
                            red: '#ef4444',
                            emerald: '#10b981',
                            amber: '#f59e0b',
                            purple: '#8b5cf6',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .pulse-urgent {
            animation: pulseSlow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulseSlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }
        .tab-btn.active {
            border-bottom: 2px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col font-sans">

    <!-- Top Notification Banner / Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"></div>

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-red-600 flex items-center justify-center text-white shadow-md shadow-rose-200">
                        <i class="fa-solid fa-heart-pulse text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900">Life<span class="text-rose-600">Link</span></span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Backend
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 hidden sm:block">Organ Donor & Compatible Matching System</p>
                    </div>
                </div>

                <!-- Active User & Session Control -->
                <div class="flex items-center gap-3">
                    <div id="user-pill" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-sm">
                        <span id="user-avatar" class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center font-bold">A</span>
                        <span id="user-display-name" class="font-medium text-slate-700">Demo Administrator</span>
                        <span id="user-role-badge" class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold capitalize">Admin</span>
                    </div>

                    <button onclick="openAuthModal()" class="px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-xs font-semibold text-slate-700 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-user-gear"></i> <span class="hidden sm:inline">Switch / Login</span>
                    </button>
                </div>
            </div>

            <!-- Tabbed Navigation -->
            <nav class="flex space-x-1 sm:space-x-8 -mb-px overflow-x-auto text-sm">
                <button onclick="switchTab('dashboard')" id="tab-dashboard" class="tab-btn active py-3 px-3 sm:px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </button>
                <button onclick="switchTab('donors')" id="tab-donors" class="tab-btn py-3 px-3 sm:px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding-heart text-rose-500"></i> Donors
                    <span id="badge-donors-count" class="ml-1 text-xs px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-700 font-bold">0</span>
                </button>
                <button onclick="switchTab('recipients')" id="tab-recipients" class="tab-btn py-3 px-3 sm:px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap flex items-center gap-2">
                    <i class="fa-solid fa-hospital-user text-amber-500"></i> Recipients
                    <span id="badge-recipients-count" class="ml-1 text-xs px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-700 font-bold">0</span>
                </button>
                <button onclick="switchTab('matching')" id="tab-matching" class="tab-btn py-3 px-3 sm:px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-indigo-500"></i> Smart Matcher
                    <span id="badge-possible-count" class="ml-1 text-xs px-1.5 py-0.2 rounded-full bg-indigo-100 text-indigo-700 font-bold">0</span>
                </button>
                <button onclick="switchTab('notifications')" id="tab-notifications" class="tab-btn py-3 px-3 sm:px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap flex items-center gap-2">
                    <i class="fa-regular fa-bell"></i> Notifications
                    <span id="badge-unread-count" class="ml-1 text-xs px-1.5 py-0.2 rounded-full bg-rose-500 text-white font-bold hidden">0</span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">

        <!-- ========================================== -->
        <!-- 1. DASHBOARD VIEW                          -->
        <!-- ========================================== -->
        <section id="view-dashboard" class="space-y-6">
            <!-- Hero Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Registered Donors</p>
                        <h3 id="stat-donors" class="text-3xl font-extrabold text-slate-800 mt-1">0</h3>
                        <p id="stat-donors-avail" class="text-xs text-emerald-600 font-medium mt-1"><i class="fa-solid fa-circle-check"></i> 0 available now</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-hand-holding-medical"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Waiting Patients</p>
                        <h3 id="stat-recipients" class="text-3xl font-extrabold text-slate-800 mt-1">0</h3>
                        <p id="stat-recipients-urgent" class="text-xs text-amber-600 font-medium mt-1"><i class="fa-solid fa-triangle-exclamation"></i> 0 critical urgency</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-bed-pulse"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Compatible Pairs</p>
                        <h3 id="stat-possible" class="text-3xl font-extrabold text-indigo-600 mt-1">0</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Matched by Blood & Organ</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-dna"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Approved Matches</p>
                        <h3 id="stat-approved" class="text-3xl font-extrabold text-emerald-600 mt-1">0</h3>
                        <p id="stat-pending" class="text-xs text-slate-500 font-medium mt-1">0 pending verification</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>

            <!-- Urgent Waiting Notice & Quick Matcher preview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Critical Waiting List -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                            <h2 class="text-lg font-bold text-slate-800">Critical & High Priority Waiting List</h2>
                        </div>
                        <button onclick="switchTab('recipients')" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View All &rarr;</button>
                    </div>
                    <div id="dashboard-urgent-list" class="divide-y divide-slate-100">
                        <p class="text-sm text-slate-400 py-4 text-center">Loading recipient priorities...</p>
                    </div>
                </div>

                <!-- Quick Action Hub -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 mb-1">Actions & Matching Engine</h2>
                        <p class="text-xs text-slate-500 mb-4">Live compatibility testing & registry operations.</p>

                        <div class="space-y-3">
                            <button onclick="openDonorModal()" class="w-full py-2.5 px-4 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold flex items-center justify-between transition border border-rose-100">
                                <span class="flex items-center gap-2"><i class="fa-solid fa-plus-circle text-rose-500"></i> Register New Donor</span>
                                <i class="fa-solid fa-chevron-right text-xs text-rose-400"></i>
                            </button>
                            <button onclick="openRecipientModal()" class="w-full py-2.5 px-4 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-semibold flex items-center justify-between transition border border-amber-100">
                                <span class="flex items-center gap-2"><i class="fa-solid fa-user-plus text-amber-500"></i> Register Recipient Patient</span>
                                <i class="fa-solid fa-chevron-right text-xs text-amber-400"></i>
                            </button>
                            <button onclick="switchTab('matching')" class="w-full py-2.5 px-4 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold flex items-center justify-between transition border border-indigo-100">
                                <span class="flex items-center gap-2"><i class="fa-solid fa-bolt text-indigo-500"></i> Run Compatibility Matcher</span>
                                <i class="fa-solid fa-chevron-right text-xs text-indigo-400"></i>
                            </button>
                        </div>
                    </div>

                    <!-- System Status Card -->
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Database: MariaDB (Port 3307)</span>
                            <span class="text-emerald-600 font-semibold"><i class="fa-solid fa-check"></i> Connected</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500 mt-2">
                            <span>REST Endpoint:</span>
                            <span class="font-mono text-slate-600">/Organ-Donor-system</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 2. DONORS VIEW                             -->
        <!-- ========================================== -->
        <section id="view-donors" class="space-y-6 hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Organ Donor Registry</h2>
                    <p class="text-xs text-slate-500">Manage volunteer donors, organ availability, and contact information.</p>
                </div>
                <button onclick="openDonorModal()" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm shadow-md shadow-rose-200 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Register Donor Profile
                </button>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Search Keyword</label>
                    <input type="text" id="filter-donor-search" oninput="renderDonorsTable()" placeholder="Name, organ, phone..." class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Blood Type</label>
                    <select id="filter-donor-blood" onchange="renderDonorsTable()" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                        <option value="">All Blood Types</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Organ</label>
                    <select id="filter-donor-organ" onchange="renderDonorsTable()" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                        <option value="">All Organs</option>
                        <option value="kidney">Kidney</option>
                        <option value="liver">Liver</option>
                        <option value="heart">Heart</option>
                        <option value="lung">Lung</option>
                        <option value="pancreas">Pancreas</option>
                        <option value="cornea">Cornea</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Availability</label>
                    <select id="filter-donor-avail" onchange="renderDonorsTable()" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </div>

            <!-- Donors Table -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Donor Name / User</th>
                                <th class="py-3 px-4">Age</th>
                                <th class="py-3 px-4">Blood Group</th>
                                <th class="py-3 px-4">Pledged Organ</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Phone</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="donors-tbody" class="divide-y divide-slate-100">
                            <tr><td colspan="8" class="text-center py-8 text-slate-400">Loading donor profiles...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 3. RECIPIENTS VIEW                         -->
        <!-- ========================================== -->
        <section id="view-recipients" class="space-y-6 hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Organ Recipient Waiting List</h2>
                    <p class="text-xs text-slate-500">Patients categorized by organ need, blood type, hospital, and medical urgency.</p>
                </div>
                <button onclick="openRecipientModal()" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm shadow-md shadow-amber-200 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add Recipient Patient
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Search Keyword</label>
                    <input type="text" id="filter-recip-search" oninput="renderRecipientsTable()" placeholder="Patient name, hospital, organ..." class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Blood Type</label>
                    <select id="filter-recip-blood" onchange="renderRecipientsTable()" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <option value="">All Blood Types</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Organ Needed</label>
                    <select id="filter-recip-organ" onchange="renderRecipientsTable()" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <option value="">All Organs</option>
                        <option value="kidney">Kidney</option>
                        <option value="liver">Liver</option>
                        <option value="heart">Heart</option>
                        <option value="lung">Lung</option>
                        <option value="pancreas">Pancreas</option>
                        <option value="cornea">Cornea</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Urgency Level</label>
                    <select id="filter-recip-urgency" onchange="renderRecipientsTable()" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <option value="">All Urgencies</option>
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <!-- Recipients Table -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Recipient Name / User</th>
                                <th class="py-3 px-4">Age</th>
                                <th class="py-3 px-4">Blood Group</th>
                                <th class="py-3 px-4">Organ Needed</th>
                                <th class="py-3 px-4">Hospital Center</th>
                                <th class="py-3 px-4">Medical Urgency</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recipients-tbody" class="divide-y divide-slate-100">
                            <tr><td colspan="8" class="text-center py-8 text-slate-400">Loading recipient list...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 4. SMART MATCHER VIEW                      -->
        <!-- ========================================== -->
        <section id="view-matching" class="space-y-6 hidden">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-blue-900 text-white p-6 rounded-2xl shadow-md">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-md bg-indigo-500/30 text-indigo-200 font-semibold text-xs border border-indigo-400/30">
                                <i class="fa-solid fa-microchip mr-1"></i> Automated Compatibility Engine
                            </span>
                            <span class="text-xs text-indigo-300">Strict Blood & Organ Matching</span>
                        </div>
                        <h2 class="text-2xl font-black tracking-tight mt-2">Compatible Match Identification</h2>
                        <p class="text-sm text-indigo-200 max-w-2xl mt-1">
                            The matching engine scans available donors against urgent waiting recipients where blood types and target organs are strictly compatible, ordered by medical urgency.
                        </p>
                    </div>
                    <button onclick="loadPossibleMatches()" class="px-5 py-2.5 rounded-xl bg-white text-indigo-900 font-bold hover:bg-indigo-50 text-sm transition shadow-lg flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-arrows-rotate"></i> Re-Scan Candidates
                    </button>
                </div>
            </div>

            <!-- Two Sub-Sections: 1. Live Candidate Pairs | 2. Official Matches Ledger -->
            <div class="space-y-6">
                <!-- Section 1: Candidate Pairs ready for matching -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Identified Compatible Pairs</h3>
                            <p class="text-xs text-slate-500">Live matching candidates ready to be scheduled for transplant approval.</p>
                        </div>
                        <span id="possible-candidates-count" class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                            0 Candidates Found
                        </span>
                    </div>

                    <div id="possible-matches-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p class="text-sm text-slate-400 col-span-2 text-center py-6">Scanning database for compatible pairs...</p>
                    </div>
                </div>

                <!-- Section 2: Official Matches Ledger -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Official Match Ledger</h3>
                            <p class="text-xs text-slate-500">Matches registered in the system with approval workflow.</p>
                        </div>
                        <button onclick="loadMatches()" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Refresh Ledger &rarr;</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4">Match ID</th>
                                    <th class="py-3 px-4">Donor</th>
                                    <th class="py-3 px-4">Recipient</th>
                                    <th class="py-3 px-4">Organ & Blood</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Created Date</th>
                                    <th class="py-3 px-4 text-right">Medical Decision</th>
                                </tr>
                            </thead>
                            <tbody id="matches-ledger-tbody" class="divide-y divide-slate-100">
                                <tr><td colspan="7" class="text-center py-8 text-slate-400">Loading matches...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 5. NOTIFICATIONS VIEW                      -->
        <!-- ========================================== -->
        <section id="view-notifications" class="space-y-6 hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Notification Center</h2>
                    <p class="text-xs text-slate-500">Transplant match alerts, approval notifications, and donor/recipient messaging.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openNotificationModal()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Send Notification
                    </button>
                    <button onclick="loadNotifications()" class="px-3 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium transition">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </button>
                </div>
            </div>

            <!-- Notifications Feed -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div id="notifications-list" class="space-y-3">
                    <p class="text-sm text-slate-400 text-center py-6">Loading notifications...</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-700">LifeLink</span> — Organ Donor & Recipient Matching System &copy; 2026
            </div>
            <div class="flex items-center gap-4">
                <a href="test-db.php" target="_blank" class="hover:text-brand-600 transition"><i class="fa-solid fa-database mr-1"></i> DB Test</a>
                <a href="index.php?api=1" target="_blank" class="hover:text-brand-600 transition"><i class="fa-solid fa-code mr-1"></i> API JSON</a>
                <a href="http://localhost/phpmyadmin/" target="_blank" class="hover:text-brand-600 transition"><i class="fa-solid fa-table mr-1"></i> phpMyAdmin</a>
            </div>
        </div>
    </footer>

    <!-- ======================================================== -->
    <!-- MODALS SECTION                                           -->
    <!-- ======================================================== -->

    <!-- Modal: Register/Edit Donor -->
    <div id="modal-donor" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 id="modal-donor-title" class="text-lg font-bold text-slate-800">Register Donor Profile</h3>
                <button onclick="closeModal('modal-donor')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>
            <form id="form-donor" onsubmit="handleDonorSubmit(event)" class="space-y-4 text-sm">
                <input type="hidden" id="donor-edit-id" value="">
                
                <div id="donor-user-id-wrapper">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Select Donor User *</label>
                    <select id="donor-user-id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                        <option value="">Choose User Account...</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Must be registered with role 'donor'.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Age (1-120) *</label>
                        <input type="number" id="donor-age" min="1" max="120" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm" placeholder="e.g. 29">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Blood Type *</label>
                        <select id="donor-blood-type" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pledged Organ *</label>
                        <select id="donor-organ" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                            <option value="kidney">Kidney</option>
                            <option value="liver">Liver</option>
                            <option value="heart">Heart</option>
                            <option value="lung">Lung</option>
                            <option value="pancreas">Pancreas</option>
                            <option value="cornea">Cornea</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Availability *</label>
                        <select id="donor-availability" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Contact Phone *</label>
                    <input type="tel" id="donor-phone" required maxlength="15" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm" placeholder="e.g. 5551000001">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-donor')" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" id="donor-submit-btn" class="px-5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Register/Edit Recipient -->
    <div id="modal-recipient" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 id="modal-recip-title" class="text-lg font-bold text-slate-800">Register Recipient Patient</h3>
                <button onclick="closeModal('modal-recipient')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>
            <form id="form-recipient" onsubmit="handleRecipientSubmit(event)" class="space-y-4 text-sm">
                <input type="hidden" id="recip-edit-id" value="">

                <div id="recip-user-id-wrapper">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Select Patient User *</label>
                    <select id="recip-user-id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <option value="">Choose User Account...</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Must be registered with role 'recipient'.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Age (1-120) *</label>
                        <input type="number" id="recip-age" min="1" max="120" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm" placeholder="e.g. 42">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Blood Type *</label>
                        <select id="recip-blood-type" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Organ Needed *</label>
                        <select id="recip-organ-needed" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="kidney">Kidney</option>
                            <option value="liver">Liver</option>
                            <option value="heart">Heart</option>
                            <option value="lung">Lung</option>
                            <option value="pancreas">Pancreas</option>
                            <option value="cornea">Cornea</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Medical Urgency *</label>
                        <select id="recip-urgency" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Hospital / Medical Center *</label>
                    <input type="text" id="recip-hospital" required maxlength="150" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm" placeholder="e.g. Demo General Hospital">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-recipient')" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" id="recip-submit-btn" class="px-5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Auth & Switch User -->
    <div id="modal-auth" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-lg font-bold text-slate-800">User Account & Quick Switcher</h3>
                <button onclick="closeModal('modal-auth')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <!-- Fast Demo Accounts Switcher -->
            <div class="mb-5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">One-Click Demo Switcher</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                    <button onclick="quickLogin('demo.admin@example.test', 'admin', 'Demo Administrator', 1)" class="p-2.5 rounded-xl border border-slate-200 hover:border-brand-500 hover:bg-brand-50 text-left transition">
                        <span class="font-bold text-slate-800 block">Admin</span>
                        <span class="text-[10px] text-slate-500 truncate block">demo.admin</span>
                    </button>
                    <button onclick="quickLogin('demo.donor.one@example.test', 'donor', 'Demo Donor One', 2)" class="p-2.5 rounded-xl border border-slate-200 hover:border-rose-500 hover:bg-rose-50 text-left transition">
                        <span class="font-bold text-rose-600 block">Donor 1 (O+)</span>
                        <span class="text-[10px] text-slate-500 truncate block">demo.donor.one</span>
                    </button>
                    <button onclick="quickLogin('demo.recipient.one@example.test', 'recipient', 'Demo Recipient One', 5)" class="p-2.5 rounded-xl border border-slate-200 hover:border-amber-500 hover:bg-amber-50 text-left transition">
                        <span class="font-bold text-amber-600 block">Recipient 1</span>
                        <span class="text-[10px] text-slate-500 truncate block">demo.recipient.one</span>
                    </button>
                </div>
            </div>

            <!-- Login / Register Tabs -->
            <div class="border-t border-slate-100 pt-4">
                <div class="flex gap-4 border-b border-slate-200 mb-4 text-sm">
                    <button type="button" onclick="setAuthTab('login')" id="auth-tab-login" class="pb-2 border-b-2 border-brand-600 font-bold text-brand-600">Login</button>
                    <button type="button" onclick="setAuthTab('register')" id="auth-tab-register" class="pb-2 border-b-2 border-transparent text-slate-500 hover:text-slate-700">Register New User</button>
                </div>

                <!-- Form: Login -->
                <form id="form-login" onsubmit="handleLoginSubmit(event)" class="space-y-3 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Email Address</label>
                        <input type="email" id="login-email" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                        <input type="password" id="login-password" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition">Sign In</button>
                </form>

                <!-- Form: Register -->
                <form id="form-register" onsubmit="handleRegisterSubmit(event)" class="space-y-3 text-sm hidden">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name</label>
                        <input type="text" id="reg-name" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm" placeholder="e.g. Jane Doe">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Email Address</label>
                        <input type="email" id="reg-email" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm" placeholder="e.g. jane@example.test">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Role</label>
                            <select id="reg-role" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                <option value="donor">Donor</option>
                                <option value="recipient">Recipient</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Password (min 8)</label>
                            <input type="password" id="reg-password" minlength="8" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition">Create Account</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Send Notification -->
    <div id="modal-notif" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-lg font-bold text-slate-800">Send Notification</h3>
                <button onclick="closeModal('modal-notif')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>
            <form id="form-notif" onsubmit="handleNotifSubmit(event)" class="space-y-4 text-sm">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Target Recipient User *</label>
                    <select id="notif-user-id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                        <option value="">Select User...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Title *</label>
                    <input type="text" id="notif-title" required maxlength="150" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm" placeholder="e.g. Urgent Compatibility Update">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Message *</label>
                    <textarea id="notif-message" required rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm" placeholder="Details of the update..."></textarea>
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-notif')" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-semibold">Send</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- JAVASCRIPT FRONTEND CONTROLLER                           -->
    <!-- ======================================================== -->
    <script>
        // API Base URL (relative to root)
        const API_BASE = '/Organ-Donor-system';

        // State Store
        const state = {
            currentUser: {
                id: 1,
                name: 'Demo Administrator',
                email: 'demo.admin@example.test',
                role: 'admin'
            },
            donors: [],
            recipients: [],
            matches: [],
            possibleMatches: [],
            notifications: [],
            allUsers: []
        };

        // Initialize App on DOM Load
        document.addEventListener('DOMContentLoaded', () => {
            initSession();
            refreshAllData();
        });

        // Initialize session from LocalStorage if available
        function initSession() {
            const saved = localStorage.getItem('lifelink_user');
            if (saved) {
                try {
                    state.currentUser = JSON.parse(saved);
                } catch(e) {}
            }
            updateUserUI();
        }

        function updateUserUI() {
            const u = state.currentUser;
            document.getElementById('user-display-name').textContent = u.name || u.email;
            document.getElementById('user-avatar').textContent = (u.name || u.email || 'U')[0].toUpperCase();
            
            const badge = document.getElementById('user-role-badge');
            badge.textContent = u.role;
            if (u.role === 'admin') {
                badge.className = 'text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-semibold capitalize';
            } else if (u.role === 'donor') {
                badge.className = 'text-xs px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-semibold capitalize';
            } else {
                badge.className = 'text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold capitalize';
            }
        }

        // Tab Switching
        function switchTab(tabId) {
            const tabs = ['dashboard', 'donors', 'recipients', 'matching', 'notifications'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-${t}`);
                const view = document.getElementById(`view-${t}`);
                if (t === tabId) {
                    btn?.classList.add('active');
                    view?.classList.remove('hidden');
                } else {
                    btn?.classList.remove('active');
                    view?.classList.add('hidden');
                }
            });

            if (tabId === 'donors') renderDonorsTable();
            if (tabId === 'recipients') renderRecipientsTable();
            if (tabId === 'matching') {
                loadPossibleMatches();
                loadMatches();
            }
            if (tabId === 'notifications') loadNotifications();
        }

        // Toasts
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const colorClasses = type === 'success' 
                ? 'bg-emerald-600 text-white' 
                : type === 'error' 
                ? 'bg-rose-600 text-white' 
                : 'bg-slate-800 text-white';

            const icon = type === 'success' ? 'fa-circle-check' : type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info';

            toast.className = `${colorClasses} pointer-events-auto px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 text-sm font-medium transition transform duration-300 translate-y-2 opacity-0`;
            toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${message}</span>`;
            
            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // API Helper
        async function api(path, options = {}) {
            try {
                const res = await fetch(`${API_BASE}${path}`, {
                    ...options,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(options.headers || {})
                    }
                });
                const data = await res.json();
                return { ok: res.ok, status: res.status, data };
            } catch (err) {
                console.error(`API Error on ${path}:`, err);
                return { ok: false, status: 500, data: { success: false, message: 'Network or server error.' } };
            }
        }

        // Refresh All Data
        async function refreshAllData() {
            await Promise.all([
                loadDonors(),
                loadRecipients(),
                loadMatches(),
                loadPossibleMatches(),
                loadNotifications()
            ]);
            updateDashboardStats();
        }

        // ==========================================
        // 1. DASHBOARD LOGIC
        // ==========================================
        function updateDashboardStats() {
            const availDonors = state.donors.filter(d => d.availability === 'available').length;
            const criticalRecip = state.recipients.filter(r => r.urgency === 'critical').length;
            const approvedMatches = state.matches.filter(m => m.match_status === 'approved').length;
            const pendingMatches = state.matches.filter(m => m.match_status === 'pending').length;

            document.getElementById('stat-donors').textContent = state.donors.length;
            document.getElementById('stat-donors-avail').innerHTML = `<i class="fa-solid fa-circle-check"></i> ${availDonors} available now`;
            
            document.getElementById('stat-recipients').textContent = state.recipients.length;
            document.getElementById('stat-recipients-urgent').innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> ${criticalRecip} critical urgency`;

            document.getElementById('stat-possible').textContent = state.possibleMatches.length;
            document.getElementById('stat-approved').textContent = approvedMatches;
            document.getElementById('stat-pending').textContent = `${pendingMatches} pending verification`;

            document.getElementById('badge-donors-count').textContent = state.donors.length;
            document.getElementById('badge-recipients-count').textContent = state.recipients.length;
            document.getElementById('badge-possible-count').textContent = state.possibleMatches.length;

            // Render Urgent List
            const urgentList = document.getElementById('dashboard-urgent-list');
            const topUrgent = state.recipients.filter(r => r.urgency === 'critical' || r.urgency === 'high').slice(0, 5);
            
            if (topUrgent.length === 0) {
                urgentList.innerHTML = `<p class="text-sm text-slate-400 py-4 text-center">No high or critical recipients currently on waitlist.</p>`;
            } else {
                urgentList.innerHTML = topUrgent.map(r => `
                    <div class="py-3 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg ${r.urgency === 'critical' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'} flex items-center justify-center font-bold text-xs uppercase">
                                ${r.blood_type}
                            </span>
                            <div>
                                <p class="font-semibold text-slate-800">${escapeHtml(r.hospital)}</p>
                                <p class="text-xs text-slate-500">Needs <strong>${escapeHtml(r.organ_needed)}</strong> &bull; Patient Age ${r.age}</p>
                            </div>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full font-bold uppercase tracking-wider ${r.urgency === 'critical' ? 'bg-rose-50 text-rose-600 border border-rose-200 pulse-urgent' : 'bg-amber-50 text-amber-700 border border-amber-200'}">
                            ${r.urgency}
                        </span>
                    </div>
                `).join('');
            }
        }

        // ==========================================
        // 2. DONORS LOGIC
        // ==========================================
        async function loadDonors() {
            const res = await api('/donors');
            if (res.ok && res.data.success) {
                state.donors = res.data.donors || [];
                renderDonorsTable();
            }
        }

        function renderDonorsTable() {
            const tbody = document.getElementById('donors-tbody');
            const search = document.getElementById('filter-donor-search').value.toLowerCase().trim();
            const blood = document.getElementById('filter-donor-blood').value;
            const organ = document.getElementById('filter-donor-organ').value.toLowerCase();
            const avail = document.getElementById('filter-donor-avail').value;

            const filtered = state.donors.filter(d => {
                if (blood && d.blood_type !== blood) return false;
                if (organ && d.organ.toLowerCase() !== organ) return false;
                if (avail && d.availability !== avail) return false;
                if (search) {
                    const matchOrgan = (d.organ || '').toLowerCase().includes(search);
                    const matchPhone = (d.phone || '').includes(search);
                    const matchId = String(d.id).includes(search);
                    if (!matchOrgan && !matchPhone && !matchId) return false;
                }
                return true;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-slate-400">No donor profiles match current filters.</td></tr>`;
                return;
            }

            tbody.innerHTML = filtered.map(d => `
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="py-3 px-4 font-bold text-slate-900">#${d.id}</td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-800">User #${d.user_id}</div>
                        <div class="text-xs text-slate-400">Registered Donor</div>
                    </td>
                    <td class="py-3 px-4 text-slate-600">${d.age} yrs</td>
                    <td class="py-3 px-4">
                        <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 font-extrabold text-xs border border-rose-200">
                            ${d.blood_type}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="capitalize font-semibold text-slate-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-notes-medical text-rose-400 text-xs"></i> ${escapeHtml(d.organ)}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <button onclick="toggleDonorAvailability(${d.id}, '${d.availability}')" title="Click to toggle availability" class="text-xs px-2.5 py-1 rounded-full font-semibold capitalize transition flex items-center gap-1.5 ${d.availability === 'available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200'}">
                            <span class="w-1.5 h-1.5 rounded-full ${d.availability === 'available' ? 'bg-emerald-500' : 'bg-slate-400'}"></span>
                            ${d.availability}
                        </button>
                    </td>
                    <td class="py-3 px-4 font-mono text-xs text-slate-600">${escapeHtml(d.phone)}</td>
                    <td class="py-3 px-4 text-right space-x-1">
                        <button onclick="editDonor(${d.id})" class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:text-brand-600 hover:border-brand-200 text-xs font-medium transition">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </button>
                        <button onclick="deleteDonor(${d.id})" class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-200 text-xs font-medium transition">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        async function toggleDonorAvailability(id, current) {
            const next = current === 'available' ? 'unavailable' : 'available';
            const res = await api(`/donors?id=${id}`, {
                method: 'PATCH',
                body: JSON.stringify({ availability: next })
            });
            if (res.ok) {
                showToast(`Donor #${id} marked as ${next}.`);
                await refreshAllData();
            } else {
                showToast(res.data.message || 'Failed to update availability', 'error');
            }
        }

        function openDonorModal(editId = null) {
            const modal = document.getElementById('modal-donor');
            const title = document.getElementById('modal-donor-title');
            const form = document.getElementById('form-donor');
            const userWrapper = document.getElementById('donor-user-id-wrapper');
            form.reset();

            if (editId) {
                const donor = state.donors.find(d => d.id === editId);
                if (!donor) return;
                title.textContent = `Edit Donor #${editId}`;
                document.getElementById('donor-edit-id').value = editId;
                userWrapper.classList.add('hidden');
                document.getElementById('donor-user-id').removeAttribute('required');
                document.getElementById('donor-age').value = donor.age;
                document.getElementById('donor-blood-type').value = donor.blood_type;
                document.getElementById('donor-organ').value = donor.organ.toLowerCase();
                document.getElementById('donor-availability').value = donor.availability;
                document.getElementById('donor-phone').value = donor.phone;
            } else {
                title.textContent = 'Register Donor Profile';
                document.getElementById('donor-edit-id').value = '';
                userWrapper.classList.remove('hidden');
                document.getElementById('donor-user-id').setAttribute('required', 'required');
                populateUserSelect('donor-user-id', 'donor');
            }

            modal.classList.remove('hidden');
        }

        function editDonor(id) {
            openDonorModal(id);
        }

        async function deleteDonor(id) {
            if (!confirm(`Are you sure you want to delete Donor Profile #${id}?`)) return;
            const res = await api(`/donors?id=${id}`, { method: 'DELETE' });
            if (res.ok) {
                showToast(`Donor #${id} deleted successfully.`);
                await refreshAllData();
            } else {
                showToast(res.data.message || 'Failed to delete donor.', 'error');
            }
        }

        async function handleDonorSubmit(e) {
            e.preventDefault();
            const editId = document.getElementById('donor-edit-id').value;
            const payload = {
                age: parseInt(document.getElementById('donor-age').value, 10),
                blood_type: document.getElementById('donor-blood-type').value,
                organ: document.getElementById('donor-organ').value,
                availability: document.getElementById('donor-availability').value,
                phone: document.getElementById('donor-phone').value
            };

            let res;
            if (editId) {
                res = await api(`/donors?id=${editId}`, {
                    method: 'PATCH',
                    body: JSON.stringify(payload)
                });
            } else {
                payload.user_id = parseInt(document.getElementById('donor-user-id').value, 10);
                res = await api('/donors', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
            }

            if (res.ok) {
                showToast(res.data.message || 'Donor profile saved successfully.');
                closeModal('modal-donor');
                await refreshAllData();
            } else {
                showToast(res.data.message || 'Failed to save donor profile.', 'error');
            }
        }

        // ==========================================
        // 3. RECIPIENTS LOGIC
        // ==========================================
        async function loadRecipients() {
            const res = await api('/recipients');
            if (res.ok && res.data.success) {
                state.recipients = res.data.recipients || [];
                renderRecipientsTable();
            }
        }

        function renderRecipientsTable() {
            const tbody = document.getElementById('recipients-tbody');
            const search = document.getElementById('filter-recip-search').value.toLowerCase().trim();
            const blood = document.getElementById('filter-recip-blood').value;
            const organ = document.getElementById('filter-recip-organ').value.toLowerCase();
            const urgency = document.getElementById('filter-recip-urgency').value;

            const filtered = state.recipients.filter(r => {
                if (blood && r.blood_type !== blood) return false;
                if (organ && r.organ_needed.toLowerCase() !== organ) return false;
                if (urgency && r.urgency !== urgency) return false;
                if (search) {
                    const matchHospital = (r.hospital || '').toLowerCase().includes(search);
                    const matchOrgan = (r.organ_needed || '').toLowerCase().includes(search);
                    const matchId = String(r.id).includes(search);
                    if (!matchHospital && !matchOrgan && !matchId) return false;
                }
                return true;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-slate-400">No recipient patients match current filters.</td></tr>`;
                return;
            }

            tbody.innerHTML = filtered.map(r => {
                const urgencyBadge = {
                    critical: 'bg-rose-50 text-rose-600 border border-rose-200 pulse-urgent font-black',
                    high: 'bg-amber-50 text-amber-700 border border-amber-200 font-bold',
                    medium: 'bg-blue-50 text-blue-700 border border-blue-200 font-semibold',
                    low: 'bg-slate-100 text-slate-600 border border-slate-200'
                }[r.urgency] || 'bg-slate-100 text-slate-600';

                return `
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="py-3 px-4 font-bold text-slate-900">#${r.id}</td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-800">User #${r.user_id}</div>
                        <div class="text-xs text-slate-400">Registered Patient</div>
                    </td>
                    <td class="py-3 px-4 text-slate-600">${r.age} yrs</td>
                    <td class="py-3 px-4">
                        <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 font-extrabold text-xs border border-amber-200">
                            ${r.blood_type}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="capitalize font-semibold text-slate-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-heart text-amber-500 text-xs"></i> ${escapeHtml(r.organ_needed)}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-slate-700 font-medium">${escapeHtml(r.hospital)}</td>
                    <td class="py-3 px-4">
                        <select onchange="updateRecipientUrgency(${r.id}, this.value)" class="text-xs px-2 py-1 rounded-full uppercase tracking-wider ${urgencyBadge} focus:outline-none cursor-pointer">
                            <option value="critical" ${r.urgency === 'critical' ? 'selected' : ''}>Critical</option>
                            <option value="high" ${r.urgency === 'high' ? 'selected' : ''}>High</option>
                            <option value="medium" ${r.urgency === 'medium' ? 'selected' : ''}>Medium</option>
                            <option value="low" ${r.urgency === 'low' ? 'selected' : ''}>Low</option>
                        </select>
                    </td>
                    <td class="py-3 px-4 text-right space-x-1">
                        <button onclick="editRecipient(${r.id})" class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:text-brand-600 hover:border-brand-200 text-xs font-medium transition">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </button>
                        <button onclick="deleteRecipient(${r.id})" class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-200 text-xs font-medium transition">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                `;
            }).join('');
        }

        async function updateRecipientUrgency(id, newUrgency) {
            const res = await api(`/recipients?id=${id}`, {
                method: 'PUT',
                body: JSON.stringify({ urgency: newUrgency })
            });
            if (res.ok) {
                showToast(`Recipient #${id} urgency set to ${newUrgency}.`);
                await refreshAllData();
            } else {
                showToast(res.data.message || 'Failed to update urgency.', 'error');
            }
        }

        function openRecipientModal(editId = null) {
            const modal = document.getElementById('modal-recipient');
            const title = document.getElementById('modal-recip-title');
            const form = document.getElementById('form-recipient');
            const userWrapper = document.getElementById('recip-user-id-wrapper');
            form.reset();

            if (editId) {
                const r = state.recipients.find(item => item.id === editId);
                if (!r) return;
                title.textContent = `Edit Recipient #${editId}`;
                document.getElementById('recip-edit-id').value = editId;
                userWrapper.classList.add('hidden');
                document.getElementById('recip-user-id').removeAttribute('required');
                document.getElementById('recip-age').value = r.age;
                document.getElementById('recip-blood-type').value = r.blood_type;
                document.getElementById('recip-organ-needed').value = r.organ_needed.toLowerCase();
                document.getElementById('recip-urgency').value = r.urgency;
                document.getElementById('recip-hospital').value = r.hospital;
            } else {
                title.textContent = 'Register Recipient Patient';
                document.getElementById('recip-edit-id').value = '';
                userWrapper.classList.remove('hidden');
                document.getElementById('recip-user-id').setAttribute('required', 'required');
                populateUserSelect('recip-user-id', 'recipient');
            }

            modal.classList.remove('hidden');
        }

        function editRecipient(id) {
            openRecipientModal(id);
        }

        async function deleteRecipient(id) {
            if (!confirm(`Are you sure you want to remove Recipient #${id} from the waiting list?`)) return;
            const res = await api(`/recipients?id=${id}`, { method: 'DELETE' });
            if (res.ok) {
                showToast(`Recipient #${id} deleted.`);
                await refreshAllData();
            } else {
                showToast(res.data.message || 'Failed to delete recipient.', 'error');
            }
        }

        async function handleRecipientSubmit(e) {
            e.preventDefault();
            const editId = document.getElementById('recip-edit-id').value;
            const payload = {
                age: parseInt(document.getElementById('recip-age').value, 10),
                blood_type: document.getElementById('recip-blood-type').value,
                organ_needed: document.getElementById('recip-organ-needed').value,
                urgency: document.getElementById('recip-urgency').value,
                hospital: document.getElementById('recip-hospital').value
            };

            let res;
            if (editId) {
                res = await api(`/recipients?id=${editId}`, {
                    method: 'PUT',
                    body: JSON.stringify(payload)
                });
            } else {
                payload.user_id = parseInt(document.getElementById('recip-user-id').value, 10);
                res = await api('/recipients', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
            }

            if (res.ok) {
                showToast(res.data.message || 'Recipient registered successfully.');
                closeModal('modal-recipient');
                await refreshAllData();
            } else {
                showToast(res.data.message || 'Failed to save recipient profile.', 'error');
            }
        }

        // ==========================================
        // 4. SMART MATCHING ENGINE LOGIC
        // ==========================================
        async function loadPossibleMatches() {
            const res = await api('/matches?action=possible');
            if (res.ok && res.data.success) {
                state.possibleMatches = res.data.possible_matches || [];
                renderPossibleMatchesGrid();
            }
        }

        function renderPossibleMatchesGrid() {
            const grid = document.getElementById('possible-matches-grid');
            const countBadge = document.getElementById('possible-candidates-count');
            countBadge.textContent = `${state.possibleMatches.length} Candidate Pair(s)`;

            if (state.possibleMatches.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-2 text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <i class="fa-solid fa-dna text-3xl text-slate-300 mb-2"></i>
                        <p class="text-sm font-semibold text-slate-600">No compatible candidates found right now</p>
                        <p class="text-xs text-slate-400 mt-1">Make sure donors have 'available' status and matching blood group & organ.</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = state.possibleMatches.map(pm => {
                const urgencyColor = {
                    critical: 'text-rose-600 bg-rose-50 border-rose-200',
                    high: 'text-amber-600 bg-amber-50 border-amber-200',
                    medium: 'text-blue-600 bg-blue-50 border-blue-200',
                    low: 'text-slate-600 bg-slate-50 border-slate-200'
                }[pm.urgency] || 'text-slate-600 bg-slate-50';

                // Check if already matched
                const isAlreadyMatched = state.matches.some(m => m.donor_id == pm.donor_id && m.recipient_id == pm.recipient_id);

                return `
                <div class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-indigo-300 transition shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full ${urgencyColor} border uppercase tracking-wider">
                            ${pm.urgency} Urgency
                        </span>
                        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 flex items-center gap-1">
                            <i class="fa-solid fa-check"></i> 100% Match: ${pm.donor_blood_type} ${pm.donor_organ}
                        </span>
                    </div>

                    <!-- Side by Side Candidate Box -->
                    <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <!-- Donor Side -->
                        <div class="border-r border-slate-200 pr-2 space-y-1">
                            <span class="font-extrabold text-rose-600 uppercase text-[10px] tracking-wide block">Donor Profile</span>
                            <p class="font-bold text-slate-800 text-sm">#${pm.donor_id} (User #${pm.donor_user_id})</p>
                            <p class="text-slate-500">Age: <strong>${pm.donor_age} yrs</strong></p>
                            <p class="text-slate-500">Blood: <span class="font-bold text-rose-600">${pm.donor_blood_type}</span></p>
                            <p class="text-slate-500">Organ: <span class="font-bold text-slate-700 capitalize">${escapeHtml(pm.donor_organ)}</span></p>
                        </div>

                        <!-- Recipient Side -->
                        <div class="pl-1 space-y-1">
                            <span class="font-extrabold text-amber-600 uppercase text-[10px] tracking-wide block">Waiting Patient</span>
                            <p class="font-bold text-slate-800 text-sm">#${pm.recipient_id} (User #${pm.recipient_user_id})</p>
                            <p class="text-slate-500">Age: <strong>${pm.recipient_age} yrs</strong></p>
                            <p class="text-slate-500">Hospital: <strong class="truncate block">${escapeHtml(pm.hospital)}</strong></p>
                            <p class="text-slate-500">Needed: <span class="font-bold text-slate-700 capitalize">${escapeHtml(pm.organ_needed)}</span></p>
                        </div>
                    </div>

                    <!-- Create Match Button -->
                    <div>
                        ${isAlreadyMatched ? `
                            <button disabled class="w-full py-2 rounded-xl bg-slate-100 text-slate-400 font-semibold text-xs border border-slate-200 cursor-not-allowed">
                                <i class="fa-solid fa-check mr-1"></i> Already in Match Ledger
                            </button>
                        ` : `
                            <button onclick="createMatch(${pm.donor_id}, ${pm.recipient_id})" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-100 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-bolt"></i> Register Official Match (Pending)
                            </button>
                        `}
                    </div>
                </div>
                `;
            }).join('');
        }

        async function createMatch(donorId, recipientId) {
            const res = await api('/matches', {
                method: 'POST',
                body: JSON.stringify({ donor_id: donorId, recipient_id: recipientId })
            });
            if (res.ok) {
                showToast('Match created successfully with status pending.');
                await refreshAllData();
            } else {
                showToast(res.data.message || 'Failed to create match.', 'error');
            }
        }

        async function loadMatches() {
            const res = await api('/matches');
            if (res.ok && res.data.success) {
                state.matches = res.data.matches || [];
                renderMatchesLedger();
            }
        }

        function renderMatchesLedger() {
            const tbody = document.getElementById('matches-ledger-tbody');
            if (state.matches.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-slate-400">No match records found in ledger.</td></tr>`;
                return;
            }

            tbody.innerHTML = state.matches.map(m => {
                const statusBadge = {
                    approved: 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold',
                    rejected: 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                    pending: 'bg-amber-50 text-amber-700 border-amber-200 font-bold'
                }[m.match_status] || 'bg-slate-100 text-slate-700';

                return `
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="py-3 px-4 font-bold text-slate-900">#${m.id}</td>
                    <td class="py-3 px-4">
                        <span class="font-semibold text-slate-800">Donor #${m.donor_id}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-semibold text-slate-800">Recipient #${m.recipient_id}</span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-700">${m.donor_blood_type}</span>
                            <span class="capitalize text-slate-600 font-medium">${escapeHtml(m.donor_organ)}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-xs px-2.5 py-1 rounded-full uppercase tracking-wider border ${statusBadge}">
                            ${m.match_status}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-xs text-slate-400 font-mono">${m.created_at || 'Just now'}</td>
                    <td class="py-3 px-4 text-right space-x-1">
                        ${m.match_status !== 'approved' ? `
                            <button onclick="updateMatchStatus(${m.id}, 'approve')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition" title="Approve match">
                                <i class="fa-solid fa-check"></i> Approve
                            </button>
                        ` : ''}
                        ${m.match_status !== 'rejected' ? `
                            <button onclick="updateMatchStatus(${m.id}, 'reject')" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-semibold transition" title="Reject match">
                                <i class="fa-solid fa-xmark"></i> Reject
                            </button>
                        ` : ''}
                    </td>
                </tr>
                `;
            }).join('');
        }

        async function updateMatchStatus(matchId, action) {
            const res = await api(`/matches?action=${action}&id=${matchId}`, {
                method: 'PATCH'
            });
            if (res.ok) {
                showToast(`Match #${matchId} marked as ${action}d.`);
                await refreshAllData();
            } else {
                showToast(res.data.message || `Failed to ${action} match.`, 'error');
            }
        }

        // ==========================================
        // 5. NOTIFICATIONS LOGIC
        // ==========================================
        async function loadNotifications() {
            // Load notifications for active user
            const userId = state.currentUser.id || 1;
            const res = await api(`/notifications?user_id=${userId}`);
            if (res.ok && res.data.success) {
                state.notifications = res.data.notifications || [];
                renderNotificationsList();
            }
        }

        function renderNotificationsList() {
            const list = document.getElementById('notifications-list');
            const unreadCount = state.notifications.filter(n => n.is_read == 0).length;
            const badge = document.getElementById('badge-unread-count');
            
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            if (state.notifications.length === 0) {
                list.innerHTML = `<p class="text-sm text-slate-400 text-center py-6">No notifications found for User #${state.currentUser.id}.</p>`;
                return;
            }

            list.innerHTML = state.notifications.map(n => `
                <div class="p-4 rounded-xl border ${n.is_read == 1 ? 'border-slate-200 bg-white' : 'border-brand-200 bg-brand-50/40'} flex items-start justify-between gap-4 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg ${n.is_read == 1 ? 'bg-slate-100 text-slate-400' : 'bg-brand-100 text-brand-600'} flex items-center justify-center text-sm mt-0.5">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-slate-800 text-sm">${escapeHtml(n.title)}</h4>
                                ${n.is_read == 0 ? '<span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-rose-500 text-white">NEW</span>' : ''}
                            </div>
                            <p class="text-xs text-slate-600 mt-1">${escapeHtml(n.message)}</p>
                            <span class="text-[11px] text-slate-400 font-mono mt-1 block">${n.created_at || 'Recently'}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        ${n.is_read == 0 ? `
                            <button onclick="markNotificationRead(${n.id})" class="text-xs px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-brand-600 hover:border-brand-200 font-medium transition" title="Mark as Read">
                                <i class="fa-solid fa-check"></i> Read
                            </button>
                        ` : ''}
                        <button onclick="deleteNotification(${n.id})" class="text-xs px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 transition" title="Delete Notification">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        async function markNotificationRead(id) {
            const res = await api(`/notifications?action=read&id=${id}`, { method: 'PATCH' });
            if (res.ok) {
                showToast('Notification marked as read.');
                await loadNotifications();
            }
        }

        async function deleteNotification(id) {
            const res = await api(`/notifications?id=${id}`, { method: 'DELETE' });
            if (res.ok) {
                showToast('Notification deleted.');
                await loadNotifications();
            }
        }

        function openNotificationModal() {
            populateUserSelect('notif-user-id');
            document.getElementById('form-notif').reset();
            document.getElementById('modal-notif').classList.remove('hidden');
        }

        async function handleNotifSubmit(e) {
            e.preventDefault();
            const payload = {
                user_id: parseInt(document.getElementById('notif-user-id').value, 10),
                title: document.getElementById('notif-title').value,
                message: document.getElementById('notif-message').value
            };
            const res = await api('/notifications', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                showToast('Notification sent successfully.');
                closeModal('modal-notif');
                await loadNotifications();
            } else {
                showToast(res.data.message || 'Failed to send notification.', 'error');
            }
        }

        // ==========================================
        // 6. AUTH & USER HELPER
        // ==========================================
        function openAuthModal() {
            document.getElementById('modal-auth').classList.remove('hidden');
        }

        function setAuthTab(tab) {
            const loginForm = document.getElementById('form-login');
            const regForm = document.getElementById('form-register');
            const tabLogin = document.getElementById('auth-tab-login');
            const tabReg = document.getElementById('auth-tab-register');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                regForm.classList.add('hidden');
                tabLogin.className = 'pb-2 border-b-2 border-brand-600 font-bold text-brand-600';
                tabReg.className = 'pb-2 border-b-2 border-transparent text-slate-500 hover:text-slate-700';
            } else {
                loginForm.classList.add('hidden');
                regForm.classList.remove('hidden');
                tabReg.className = 'pb-2 border-b-2 border-brand-600 font-bold text-brand-600';
                tabLogin.className = 'pb-2 border-b-2 border-transparent text-slate-500 hover:text-slate-700';
            }
        }

        function quickLogin(email, role, name, id) {
            state.currentUser = { id, email, role, name };
            localStorage.setItem('lifelink_user', JSON.stringify(state.currentUser));
            updateUserUI();
            closeModal('modal-auth');
            showToast(`Switched active user to ${name} (${role})`);
            loadNotifications();
        }

        async function handleLoginSubmit(e) {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            const res = await api('/users?action=login', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });

            if (res.ok && res.data.success) {
                const u = res.data.user;
                state.currentUser = {
                    id: u.id,
                    email: u.email,
                    role: u.role,
                    name: u.full_name
                };
                localStorage.setItem('lifelink_user', JSON.stringify(state.currentUser));
                updateUserUI();
                closeModal('modal-auth');
                showToast(`Welcome back, ${u.full_name}!`);
                loadNotifications();
            } else {
                showToast(res.data.message || 'Login failed. Check credentials.', 'error');
            }
        }

        async function handleRegisterSubmit(e) {
            e.preventDefault();
            const fullName = document.getElementById('reg-name').value;
            const email = document.getElementById('reg-email').value;
            const role = document.getElementById('reg-role').value;
            const password = document.getElementById('reg-password').value;

            const res = await api('/users', {
                method: 'POST',
                body: JSON.stringify({ full_name: fullName, email, role, password })
            });

            if (res.ok && res.data.success) {
                showToast('User registered successfully! You may now sign in.');
                document.getElementById('login-email').value = email;
                setAuthTab('login');
            } else {
                showToast(res.data.message || 'Registration failed.', 'error');
            }
        }

        // Populate User Select Dropdown for Forms
        function populateUserSelect(selectId, filterRole = null) {
            const sel = document.getElementById(selectId);
            if (!sel) return;
            
            // Hardcoded fallback demo users if not fetched
            const users = [
                { id: 1, full_name: 'Demo Administrator', role: 'admin' },
                { id: 2, full_name: 'Demo Donor One', role: 'donor' },
                { id: 3, full_name: 'Demo Donor Two', role: 'donor' },
                { id: 4, full_name: 'Demo Donor Three', role: 'donor' },
                { id: 5, full_name: 'Demo Recipient One', role: 'recipient' },
                { id: 6, full_name: 'Demo Recipient Two', role: 'recipient' },
                { id: 7, full_name: 'Demo Recipient Three', role: 'recipient' }
            ];

            const filtered = filterRole ? users.filter(u => u.role === filterRole) : users;
            sel.innerHTML = `<option value="">Choose User...</option>` + filtered.map(u => `
                <option value="${u.id}">#${u.id} - ${u.full_name} (${u.role})</option>
            `).join('');
        }

        // Generic Modal Closer
        function closeModal(modalId) {
            document.getElementById(modalId)?.classList.add('hidden');
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
