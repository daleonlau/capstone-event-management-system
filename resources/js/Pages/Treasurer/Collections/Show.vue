<template>
  <OrganizationUserLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Glassmorphism Effect -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center space-x-4">
              <Link href="/treasurer/collections" 
                    class="group flex items-center justify-center w-10 h-10 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                <svg class="w-5 h-5 text-gray-600 group-hover:text-emerald-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
              </Link>
              <div>
                <div class="flex items-center gap-3">
                  <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    {{ event.event_name }}
                  </h1>
                  <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                    Fee: ₱{{ formatNumber(event.event_fee) }}
                  </span>
                </div>
                <p class="text-gray-500 mt-1 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                  Cash Collection Management
                </p>
              </div>
            </div>
            
            <div class="flex gap-3">
              <Link :href="`/treasurer/collections/${event.id}/summary`" 
                    class="group relative px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 hover:shadow-lg hover:scale-105 flex items-center gap-2 overflow-hidden">
                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                View Analytics
              </Link>
            </div>
          </div>
        </div>

        <!-- Animated Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Students Card -->
          <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                  <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Eligible</span>
              </div>
              <p class="text-sm text-gray-500 mb-1">Total Students</p>
              <p class="text-3xl font-bold text-gray-800">{{ summary.total_students }}</p>
              <div class="mt-2 h-1 bg-blue-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" :style="{ width: '100%' }"></div>
              </div>
            </div>
          </div>

          <!-- Paid Card -->
          <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                  <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">Collected</span>
              </div>
              <p class="text-sm text-gray-500 mb-1">Paid Students</p>
              <p class="text-3xl font-bold text-gray-800">{{ summary.paid_count }}</p>
              <div class="mt-2 h-1 bg-green-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-600 rounded-full" :style="{ width: (summary.paid_count / summary.total_students * 100) + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- Pending Card -->
          <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">Awaiting</span>
              </div>
              <p class="text-sm text-gray-500 mb-1">Pending</p>
              <p class="text-3xl font-bold text-gray-800">{{ summary.pending_count }}</p>
              <div class="mt-2 h-1 bg-yellow-100 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-600 rounded-full" :style="{ width: (summary.pending_count / summary.total_students * 100) + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- Total Collected Card -->
          <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6 text-white">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-white bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                  {{ summary.collection_rate }}% Complete
                </span>
              </div>
              <p class="text-sm text-white/80 mb-1">Total Collected</p>
              <p class="text-3xl font-bold">₱{{ formatNumber(summary.total_collected) }}</p>
              <div class="mt-2 h-1 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white rounded-full" :style="{ width: summary.collection_rate + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bar with Expected Total -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div>
              <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Collection Progress
              </h3>
            </div>
            <div class="flex items-center gap-4 text-sm">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-600 rounded-full"></span>
                <span class="text-gray-600">Collected: ₱{{ formatNumber(summary.total_collected) }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-gray-300 rounded-full"></span>
                <span class="text-gray-600">Expected: ₱{{ formatNumber(summary.expected_total) }}</span>
              </div>
            </div>
          </div>
          
          <div class="relative">
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
              <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-4 transition-all duration-1000 ease-out relative"
                   :style="{ width: summary.collection_rate + '%' }">
                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
              </div>
            </div>
            
            <!-- Milestone Markers -->
            <div class="flex justify-between mt-2 px-1">
              <div class="text-xs text-gray-500">0%</div>
              <div class="text-xs text-gray-500">25%</div>
              <div class="text-xs text-gray-500">50%</div>
              <div class="text-xs text-gray-500">75%</div>
              <div class="text-xs text-gray-500">100%</div>
            </div>
          </div>
        </div>

        <!-- Search and Actions Bar -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-4 mb-8">
          <div class="flex flex-wrap items-center gap-4">
            <!-- Search with Icon -->
            <div class="flex-1 min-w-[300px]">
              <div class="relative group">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search by ID, Name, or Email..."
                  class="w-full pl-12 pr-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-300 outline-none"
                  @keyup.enter="applySearch"
                />
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button v-if="search" @click="clearSearch" class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Quick Stats Pills -->
            <div class="flex items-center gap-2">
              <div class="px-4 py-2 bg-green-50 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                {{ summary.paid_count }} Paid
              </div>
              <div class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-xl text-sm font-medium flex items-center gap-2">
                <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                {{ summary.pending_count }} Pending
              </div>
            </div>

            <!-- Bulk Actions Toggle -->
            <button
              v-if="selectedStudents.length > 0"
              @click="confirmBulkPay"
              class="group relative px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all duration-300 hover:shadow-lg hover:scale-105 flex items-center gap-2 overflow-hidden"
            >
              <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              <span>Bulk Pay ({{ selectedStudents.length }})</span>
            </button>

            <!-- Email Toggle -->
            <label class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
              <input type="checkbox" v-model="bulkSendEmail" class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
              <span class="text-sm text-gray-700">Send receipts via email</span>
            </label>
          </div>
        </div>

        <!-- Modern Table with Cards on Mobile -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                  <th class="px-6 py-4 text-left">
                    <input
                      type="checkbox"
                      :checked="allSelected"
                      @change="toggleAll"
                      :disabled="isAllPaid"
                      class="w-4 h-4 text-emerald-600 rounded border-2 border-gray-300 focus:ring-emerald-500 cursor-pointer"
                    />
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Program & Year</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Receipt</th>
                  <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="student in students.data" :key="student.student_id" 
                    class="hover:bg-gray-50 transition-colors group">
                  <td class="px-6 py-4">
                    <input
                      type="checkbox"
                      :value="student.student_id"
                      v-model="selectedStudents"
                      :disabled="student.payment_status === 'Paid'"
                      class="w-4 h-4 text-emerald-600 rounded border-2 border-gray-300 focus:ring-emerald-500 cursor-pointer"
                    />
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <span class="text-white text-sm font-bold">{{ student.firstname?.charAt(0) }}{{ student.lastname?.charAt(0) }}</span>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">{{ student.firstname }} {{ student.lastname }}</div>
                        <div class="text-xs text-gray-500">{{ student.student_id }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ student.email || 'No email' }}</div>
                    <div class="text-xs text-gray-500">{{ student.department || 'No college' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ student.course }}</div>
                    <div class="mt-1">
                      <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                        Year {{ student.yearlevel }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="statusBadgeClass(student.payment_status)" class="px-3 py-1 text-xs font-medium rounded-full shadow-sm">
                      {{ student.payment_status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div v-if="student.receipt_number" class="flex items-center gap-2">
                      <span class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded">
                        {{ student.receipt_number.slice(-8) }}
                      </span>
                      <div class="flex items-center gap-1">
                        <button @click="viewReceipt(event.id, student.student_id)" 
                                class="p-1.5 text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-all" 
                                title="View Receipt">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                        </button>
                        <button @click="downloadReceipt(event.id, student.student_id)" 
                                class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all" 
                                title="Download Receipt">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                          </svg>
                        </button>
                        <button @click="resendReceipt(event.id, student.student_id)" 
                                class="p-1.5 text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all" 
                                :class="{ 'opacity-50 cursor-not-allowed': !student.email }"
                                :title="student.email ? 'Resend Email' : 'No email address'"
                                :disabled="!student.email">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                          </svg>
                        </button>
                      </div>
                    </div>
                    <span v-else class="text-xs text-gray-400">No receipt</span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <button
                      v-if="student.payment_status !== 'Paid'"
                      @click="openPaymentModal(student)"
                      class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 hover:shadow-lg hover:scale-105 text-sm font-medium flex items-center gap-2 mx-auto"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      Receive Cash
                    </button>
                    <div v-else class="inline-flex items-center gap-1 text-sm text-gray-400">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Paid</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="md:hidden divide-y divide-gray-200">
            <div v-for="student in students.data" :key="student.student_id" class="p-4 hover:bg-gray-50 transition-colors">
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                  <input
                    type="checkbox"
                    :value="student.student_id"
                    v-model="selectedStudents"
                    :disabled="student.payment_status === 'Paid'"
                    class="w-4 h-4 text-emerald-600 rounded border-2 border-gray-300 focus:ring-emerald-500"
                  />
                  <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white text-sm font-bold">{{ student.firstname?.charAt(0) }}{{ student.lastname?.charAt(0) }}</span>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">{{ student.firstname }} {{ student.lastname }}</div>
                    <div class="text-xs text-gray-500">{{ student.student_id }}</div>
                  </div>
                </div>
                <span :class="statusBadgeClass(student.payment_status)" class="px-2 py-1 text-xs font-medium rounded-full">
                  {{ student.payment_status }}
                </span>
              </div>

              <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                <div>
                  <span class="text-gray-500">Program:</span>
                  <span class="ml-1 text-gray-900">{{ student.course }}</span>
                </div>
                <div>
                  <span class="text-gray-500">Year:</span>
                  <span class="ml-1 text-gray-900">{{ student.yearlevel }}</span>
                </div>
                <div class="col-span-2">
                  <span class="text-gray-500">Email:</span>
                  <span class="ml-1 text-gray-900">{{ student.email || 'No email' }}</span>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <div v-if="student.receipt_number" class="flex items-center gap-2">
                  <span class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded">
                    {{ student.receipt_number.slice(-8) }}
                  </span>
                  <div class="flex items-center gap-1">
                    <button @click="viewReceipt(event.id, student.student_id)" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <button @click="downloadReceipt(event.id, student.student_id)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                      </svg>
                    </button>
                  </div>
                </div>
                <button
                  v-if="student.payment_status !== 'Paid'"
                  @click="openPaymentModal(student)"
                  class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm"
                >
                  Receive Cash
                </button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="students.data.length === 0" class="py-16 text-center">
            <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
              <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No students found</h3>
            <p class="text-gray-500">Try adjusting your search or filters</p>
          </div>

          <!-- Pagination -->
          <div v-if="students.links && students.links.length > 3" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="text-sm text-gray-500">
                Showing <span class="font-medium">{{ students.from }}</span> to <span class="font-medium">{{ students.to }}</span> of <span class="font-medium">{{ students.total }}</span> entries
              </div>
              <div class="flex gap-2">
                <button
                  v-for="(link, index) in students.links"
                  :key="index"
                  v-html="link.label"
                  @click="goToPage(link.url)"
                  class="px-4 py-2 text-sm border rounded-xl hover:bg-white transition disabled:opacity-50"
                  :class="{ 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700': link.active, 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active }"
                  :disabled="!link.url"
                ></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modern Payment Modal with Blurry Background -->
        <Teleport to="body">
          <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div v-if="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto">
              <!-- Blurry backdrop -->
              <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showPaymentModal = false"></div>
              
              <!-- Modal container -->
              <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                  <!-- Header with gradient -->
                  <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                      <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Confirm Cash Payment
                      </h3>
                      <button @click="showPaymentModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Modal Body -->
                  <div class="p-6">
                    <!-- Warning Alert -->
                    <div class="mb-4 rounded-lg bg-yellow-50 p-4">
                      <div class="flex">
                        <div class="flex-shrink-0">
                          <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          </svg>
                        </div>
                        <div class="ml-3">
                          <p class="text-sm text-yellow-700">
                            Verify you have received <span class="font-bold">₱{{ formatNumber(event.event_fee) }}</span> cash from the student before proceeding.
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Student Info Card -->
                    <div class="mb-4 rounded-lg bg-gray-50 p-4">
                      <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                          <span class="text-lg font-bold text-emerald-700">
                            {{ selectedStudent?.firstname?.charAt(0) }}{{ selectedStudent?.lastname?.charAt(0) }}
                          </span>
                        </div>
                        <div>
                          <h4 class="font-medium text-gray-900">{{ selectedStudent?.firstname }} {{ selectedStudent?.lastname }}</h4>
                          <p class="text-sm text-gray-500">{{ selectedStudent?.student_id }}</p>
                        </div>
                      </div>
                      
                      <div class="mt-3 space-y-1 text-sm">
                        <p><span class="text-gray-500">Program:</span> <span class="font-medium text-gray-900">{{ selectedStudent?.course }}</span></p>
                        <p><span class="text-gray-500">Year Level:</span> <span class="font-medium text-gray-900">{{ selectedStudent?.yearlevel }}</span></p>
                        <p><span class="text-gray-500">Email:</span> <span class="font-medium text-gray-900">{{ selectedStudent?.email || 'No email' }}</span></p>
                      </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="mb-4 rounded-lg bg-emerald-50 p-4">
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Amount to Collect:</span>
                        <span class="text-xl font-bold text-emerald-700">₱{{ formatNumber(event.event_fee) }}</span>
                      </div>
                      <div class="mt-2 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Payment Method:</span>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700">Cash</span>
                      </div>
                    </div>

                    <!-- Notes Field -->
                    <div class="mb-4">
                      <label class="mb-1 block text-sm font-medium text-gray-700">Notes (Optional)</label>
                      <textarea
                        v-model="paymentForm.notes"
                        rows="2"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        placeholder="Add any notes about this payment..."
                      ></textarea>
                    </div>

                    <!-- Email Option -->
                    <label class="mb-4 flex cursor-pointer items-center gap-3 rounded-lg bg-gray-50 p-3 hover:bg-gray-100">
                      <input
                        type="checkbox"
                        v-model="paymentForm.send_email"
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                        :checked="selectedStudent?.email ? true : false"
                        :disabled="!selectedStudent?.email"
                      />
                      <span class="text-sm text-gray-700">Send receipt via email</span>
                    </label>
                  </div>

                  <!-- Footer -->
                  <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                    <button
                      @click="showPaymentModal = false"
                      class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    >
                      Cancel
                    </button>
                    <button
                      @click="processSinglePayment"
                      class="rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-2 text-sm font-medium text-white hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
                      :disabled="paymentForm.processing"
                    >
                      <span v-if="paymentForm.processing">Processing...</span>
                      <span v-else>Confirm Payment</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </Teleport>

        <!-- Bulk Payment Modal -->
        <Teleport to="body">
          <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div v-if="showBulkPaymentModal" class="fixed inset-0 z-50 overflow-y-auto">
              <!-- Blurry backdrop -->
              <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showBulkPaymentModal = false"></div>
              
              <!-- Modal container -->
              <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                  <!-- Header -->
                  <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                      <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Bulk Payment Confirmation
                      </h3>
                      <button @click="showBulkPaymentModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Body -->
                  <div class="p-6">
                    <div class="mb-4 rounded-lg bg-yellow-50 p-4">
                      <div class="flex">
                        <div class="flex-shrink-0">
                          <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          </svg>
                        </div>
                        <div class="ml-3">
                          <p class="text-sm text-yellow-700">
                            You are about to mark <span class="font-bold">{{ selectedStudents.length }} students</span> as paid via cash.
                          </p>
                          <p class="mt-1 text-sm font-bold text-yellow-800">
                            Total amount: ₱{{ formatNumber(event.event_fee * selectedStudents.length) }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <div class="mb-4 rounded-lg bg-emerald-50 p-4">
                      <label class="flex cursor-pointer items-center gap-3">
                        <input type="checkbox" v-model="bulkSendEmail" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-medium text-gray-700">Send receipts via email to students</span>
                      </label>
                    </div>

                    <div class="rounded-lg bg-blue-50 p-4">
                      <p class="text-xs text-blue-700">
                        <span class="font-semibold">Note:</span> Receipts will be generated for all students. 
                        {{ bulkSendEmail ? 'Emails will be sent to students with valid email addresses.' : 'No emails will be sent.' }}
                      </p>
                    </div>
                  </div>

                  <!-- Footer -->
                  <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                    <button
                      @click="showBulkPaymentModal = false"
                      class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    >
                      Cancel
                    </button>
                    <button
                      @click="processBulkPayment"
                      class="rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-2 text-sm font-medium text-white hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
                      :disabled="bulkProcessing"
                    >
                      <span v-if="bulkProcessing">Processing...</span>
                      <span v-else>Confirm Bulk Payment</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </Teleport>

        <!-- Toast Notifications -->
        <Transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="translate-x-full opacity-0"
          enter-to-class="translate-x-0 opacity-100"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="translate-x-0 opacity-100"
          leave-to-class="translate-x-full opacity-0"
        >
          <div v-if="toast.show" 
               class="fixed bottom-4 right-4 z-50 flex min-w-[320px] items-center gap-3 rounded-xl border-l-4 p-4 shadow-2xl backdrop-blur-sm"
               :class="toast.type === 'success' ? 'border-green-500 bg-green-50 text-green-800' : 'border-red-500 bg-red-50 text-red-800'">
            <div class="flex-shrink-0">
              <svg v-if="toast.type === 'success'" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <span class="flex-1">{{ toast.message }}</span>
            <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </Transition>
      </div>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import axios from 'axios';

// Helper function to get CSRF token
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

const props = defineProps({
  event: {
    type: Object,
    required: true
  },
  students: {
    type: Object,
    default: () => ({ data: [] })
  },
  summary: {
    type: Object,
    default: () => ({})
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

// State
const search = ref(props.filters?.search || '');
const selectedStudents = ref([]);
const showPaymentModal = ref(false);
const showBulkPaymentModal = ref(false);
const selectedStudent = ref(null);
const bulkSendEmail = ref(true);
const bulkProcessing = ref(false);

// Toast notification
const toast = ref({
  show: false,
  message: '',
  type: 'success'
});

// Payment Form
const paymentForm = ref({
  notes: '',
  send_email: true,
  processing: false
});

// Computed
const allSelected = computed({
  get: () => {
    const selectableStudents = props.students.data?.filter(s => s.payment_status !== 'Paid') || [];
    return selectableStudents.length > 0 && selectedStudents.value.length === selectableStudents.length;
  },
  set: (value) => {
    if (value) {
      const unpaidStudents = props.students.data?.filter(s => s.payment_status !== 'Paid').map(s => s.student_id) || [];
      selectedStudents.value = unpaidStudents;
    } else {
      selectedStudents.value = [];
    }
  }
});

const isAllPaid = computed(() => {
  return props.students.data?.every(s => s.payment_status === 'Paid') || false;
});

// Methods
function formatNumber(num) {
  if (num === null || num === undefined) return '0.00';
  return Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function statusBadgeClass(status) {
  const base = 'px-3 py-1 text-xs font-medium rounded-full shadow-sm';
  switch(status) {
    case 'Paid': return `${base} bg-gradient-to-r from-green-100 to-green-50 text-green-700 border border-green-200`;
    case 'Pending': return `${base} bg-gradient-to-r from-yellow-100 to-yellow-50 text-yellow-700 border border-yellow-200`;
    case 'Not Paid': return `${base} bg-gradient-to-r from-red-100 to-red-50 text-red-700 border border-red-200`;
    default: return `${base} bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700 border border-gray-200`;
  }
}

function toggleAll() {
  allSelected.value = !allSelected.value;
}

function applySearch() {
  router.get(`/treasurer/collections/${props.event.id}`, { search: search.value }, { preserveState: true });
}

function clearSearch() {
  search.value = '';
  applySearch();
}

function goToPage(url) {
  if (url) router.visit(url, { preserveState: true });
}

function openPaymentModal(student) {
  selectedStudent.value = student;
  paymentForm.value = {
    notes: '',
    send_email: !!student.email,
    processing: false
  };
  showPaymentModal.value = true;
}

function showToast(message, type = 'success') {
  toast.value = {
    show: true,
    message,
    type
  };
  setTimeout(() => {
    toast.value.show = false;
  }, 5000);
}

async function processSinglePayment() {
  if (!selectedStudent.value) return;
  
  paymentForm.value.processing = true;
  
  try {
    const response = await axios.post(
      `/treasurer/collections/${props.event.id}/${selectedStudent.value.student_id}/pay`,
      {
        notes: paymentForm.value.notes,
        send_email: paymentForm.value.send_email
      },
      {
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'X-Requested-With': 'XMLHttpRequest'
        }
      }
    );
    
    showPaymentModal.value = false;
    selectedStudent.value = null;
    
    showToast(`✅ Payment successful! Receipt #: ${response.data.receipt_number}`, 'success');
    
    setTimeout(() => {
      window.location.reload();
    }, 2000);
    
  } catch (error) {
    console.error('Payment error:', error);
    const errorMessage = error.response?.data?.error || error.message || 'Unknown error occurred';
    showToast(`❌ Error: ${errorMessage}`, 'error');
  } finally {
    paymentForm.value.processing = false;
  }
}

function confirmBulkPay() {
  if (selectedStudents.value.length === 0) return;
  showBulkPaymentModal.value = true;
}

async function processBulkPayment() {
  if (selectedStudents.value.length === 0) return;
  
  bulkProcessing.value = true;
  
  try {
    const response = await axios.post(
      `/treasurer/collections/${props.event.id}/bulk-pay`,
      {
        student_ids: selectedStudents.value,
        send_email: bulkSendEmail.value
      },
      {
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'X-Requested-With': 'XMLHttpRequest'
        }
      }
    );
    
    showBulkPaymentModal.value = false;
    selectedStudents.value = [];
    
    showToast(`✅ ${response.data.message}`, 'success');
    
    setTimeout(() => {
      window.location.reload();
    }, 2000);
    
  } catch (error) {
    console.error('Bulk payment error:', error);
    const errorMessage = error.response?.data?.error || error.message || 'Unknown error occurred';
    showToast(`❌ Error: ${errorMessage}`, 'error');
  } finally {
    bulkProcessing.value = false;
  }
}

// Receipt Actions
function viewReceipt(eventId, studentId) {
  window.open(`/treasurer/receipts/${eventId}/${studentId}/view`, '_blank');
}

function downloadReceipt(eventId, studentId) {
  window.open(`/treasurer/receipts/${eventId}/${studentId}/download`, '_blank');
}

async function resendReceipt(eventId, studentId) {
  const student = props.students.data.find(s => s.student_id === studentId);
  
  if (!student?.email) {
    showToast('❌ Student has no email address', 'error');
    return;
  }
  
  if (!confirm(`Resend receipt email to ${student.email}?`)) return;
  
  try {
    const response = await axios.post(
      `/treasurer/receipts/${eventId}/${studentId}/resend`,
      {},
      {
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'X-Requested-With': 'XMLHttpRequest'
        }
      }
    );
    showToast('✅ Receipt email resent successfully', 'success');
  } catch (error) {
    console.error('Resend error:', error);
    const errorMessage = error.response?.data?.error || error.message || 'Unknown error occurred';
    showToast(`❌ Error: ${errorMessage}`, 'error');
  }
}
</script>