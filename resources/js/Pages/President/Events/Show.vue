<template>
  <OrganizationUserLayout>
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Event Details</h1>
          <p class="text-gray-500 mt-1">{{ event.event_name }}</p>
        </div>
        <div class="flex gap-2">
          <Link href="/president/events" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
          </Link>
          <Link :href="`/president/events/${event.id}/edit`" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
          </Link>
          <button 
            v-if="stats.can_be_finished"
            @click="confirmMarkFinished"
            class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Mark as Finished
          </button>
        </div>
      </div>

      <!-- Status Alerts -->
      <div v-if="event.approval_status === 'pending_document'" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-yellow-700">
              <span class="font-medium">Action Required:</span> This event needs a signed document before it can be sent for adviser approval.
            </p>
          </div>
        </div>
      </div>

      <div v-if="event.approval_status === 'pending_approval'" class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-blue-700">
              <span class="font-medium">Pending Approval:</span> Document uploaded. Waiting for adviser review.
            </p>
          </div>
        </div>
      </div>

      <div v-if="event.approval_status === 'approved'" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">
              <span class="font-medium">Approved!</span> This event has been approved by the adviser.
            </p>
          </div>
        </div>
      </div>

      <div v-if="event.approval_status === 'rejected'" class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">
              <span class="font-medium">Rejected:</span> {{ event.rejection_reason || 'No reason provided' }}
            </p>
          </div>
        </div>
      </div>

      <div v-if="event.status === 'Finished'" class="bg-purple-50 border-l-4 border-purple-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-purple-700">
              <span class="font-medium">Event Finished!</span> This event has been marked as finished. You can now request evaluation services.
            </p>
          </div>
        </div>
      </div>

      <!-- Request Evaluation Button -->
      <div v-if="event.approval_status === 'approved' && !hasEvaluationRequest && event.status !== 'Finished'" class="mt-4">
        <button 
          @click="openRequestModal"
          class="w-full md:w-auto px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center justify-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Request Evaluation Service
        </button>
      </div>

      <!-- Evaluation Request Status -->
      <div v-if="hasEvaluationRequest" class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-blue-700">
              <span class="font-medium">Evaluation Request Status:</span> 
              <span v-if="evaluationRequestStatus === 'pending'">Your request is pending review by QUAMS.</span>
              <span v-else-if="evaluationRequestStatus === 'processing'">QUAMS is now creating your evaluation form.</span>
              <span v-else-if="evaluationRequestStatus === 'completed'">Evaluation form is ready! You can view results in the Evaluations section.</span>
            </p>
          </div>
        </div>
      </div>

      <!-- Event Header Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-700"></div>
        <div class="px-8 pb-8">
          <div class="flex items-end -mt-12 mb-6">
            <div class="w-24 h-24 bg-white rounded-2xl shadow-lg p-1">
              <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center">
                <span class="text-white text-3xl font-bold">{{ event.event_name?.charAt(0) }}</span>
              </div>
            </div>
            <div class="ml-6 flex-1">
              <h2 class="text-2xl font-bold text-gray-800">{{ event.event_name }}</h2>
              <div class="flex items-center gap-3 mt-2">
                <span :class="statusBadgeClass(event.status)" class="px-3 py-1 text-xs rounded-full">{{ event.status }}</span>
                <span :class="approvalBadgeClass(event.approval_status)" class="px-3 py-1 text-xs rounded-full">{{ formatApprovalStatus(event.approval_status) }}</span>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Event Type</p>
              <p class="text-lg font-semibold text-gray-800">{{ event.event_type?.name }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Start Date</p>
              <p class="text-lg font-semibold text-gray-800">{{ formatDate(event.event_date_start) }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">End Date</p>
              <p class="text-lg font-semibold text-gray-800">{{ formatDate(event.event_date_end) }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Event Fee</p>
              <p class="text-lg font-semibold text-gray-800">{{ event.payment === 'Payment' ? `₱${event.event_fee}` : 'Free' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Target Audience -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Target Audience</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Departments
            </h3>
            <div class="flex flex-wrap gap-2">
              <template v-if="event.departments && event.departments.length > 0">
                <span 
                  v-for="deptId in event.departments" 
                  :key="deptId" 
                  class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm"
                >
                  {{ getDepartmentName(deptId) }}
                </span>
              </template>
              <p v-else class="text-gray-400 text-sm">No departments selected</p>
            </div>
          </div>

          <div>
            <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Courses
            </h3>
            <div class="flex flex-wrap gap-2">
              <template v-if="event.courses && event.courses.length > 0">
                <span 
                  v-for="courseId in event.courses" 
                  :key="courseId" 
                  class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm"
                >
                  {{ getCourseName(courseId) }}
                </span>
              </template>
              <p v-else class="text-gray-400 text-sm">No courses selected</p>
            </div>
          </div>

          <div>
            <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
              </svg>
              Year Levels
            </h3>
            <div class="flex flex-wrap gap-2">
              <template v-if="event.year_levels && event.year_levels.length > 0">
                <span 
                  v-for="year in event.year_levels" 
                  :key="year" 
                  class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm"
                >
                  {{ year }}
                </span>
              </template>
              <p v-else class="text-gray-400 text-sm">No year levels selected</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Upload Section -->
      <div v-if="!event.has_document" class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Upload Signed Document</h2>
        
        <form @submit.prevent="uploadDocument" enctype="multipart/form-data" class="space-y-4">
          <div 
            class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-emerald-500 transition cursor-pointer"
            @dragover.prevent
            @drop.prevent="handleDocumentDrop"
            @click="$refs.documentInput.click()"
          >
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            
            <p class="text-gray-600 mb-2">
              <span class="font-semibold">Click to upload</span> or drag and drop
            </p>
            <p class="text-sm text-gray-500">
              {{ documentFileName || 'PDF, JPG, or PNG (Max 5MB)' }}
            </p>
          </div>

          <input
            ref="documentInput"
            type="file"
            @change="handleDocumentFile"
            accept=".pdf,.jpg,.jpeg,.png"
            class="hidden"
          />

          <div v-if="uploadForm.errors.signed_document" class="text-sm text-red-600">
            {{ uploadForm.errors.signed_document }}
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition disabled:opacity-50 flex items-center gap-2"
              :disabled="uploadForm.processing || !uploadForm.signed_document"
            >
              <svg v-if="uploadForm.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ uploadForm.processing ? 'Uploading...' : 'Upload Document' }}</span>
            </button>
          </div>
        </form>

        <p class="text-sm text-gray-500 mt-4">
          After uploading the document, the event will be sent to the adviser for approval.
        </p>
      </div>

      <!-- Document Display -->
      <div v-else class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Signed Document</h2>
        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
          <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-700">Document uploaded</p>
            <p class="text-xs text-gray-500">Uploaded on {{ formatDate(event.updated_at) }}</p>
          </div>
          <a :href="`/storage/${event.signed_document_path}`" target="_blank" 
             class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            View Document
          </a>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="border-b border-gray-200">
        <nav class="flex gap-4">
          <button 
            @click="activeTab = 'students'" 
            :class="activeTab === 'students' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
            class="px-4 py-2 border-b-2 font-medium transition flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Eligible Students ({{ eligibleStudents.length }})
          </button>
          <button 
            @click="activeTab = 'guests'" 
            :class="activeTab === 'guests' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
            class="px-4 py-2 border-b-2 font-medium transition flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Guest Respondents ({{ eligibleGuests?.length || 0 }})
          </button>
        </nav>
      </div>

      <!-- Students Tab -->
      <div v-if="activeTab === 'students'" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Eligible Students
              <span class="text-sm font-normal text-gray-500">(Based on selected departments, courses, and year levels)</span>
            </h3>
            <button 
              @click="refreshEligibleStudents"
              :disabled="refreshing"
              class="px-3 py-1.5 text-sm bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition flex items-center gap-2 disabled:opacity-50"
            >
              <svg v-if="refreshing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Refresh List
            </button>
          </div>
        </div>
        
        <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
          <div v-for="student in eligibleStudents" :key="student.student_id" class="p-4 hover:bg-gray-50 transition">
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <p class="font-medium text-gray-800">{{ student.firstname }} {{ student.lastname }}</p>
                <p class="text-sm text-gray-500">{{ student.student_id }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                  <span class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full">{{ student.department }}</span>
                  <span class="text-xs px-2 py-1 bg-green-50 text-green-700 rounded-full">{{ student.course }}</span>
                  <span class="text-xs px-2 py-1 bg-purple-50 text-purple-700 rounded-full">{{ student.yearlevel }}</span>
                </div>
              </div>
              <div class="text-right">
                <span :class="[
                  'px-3 py-1 text-xs font-medium rounded-full',
                  student.status === 'Paid' ? 'bg-green-100 text-green-700' :
                  student.status === 'Pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700'
                ]">
                  {{ student.status }}
                </span>
                <p v-if="student.amount_paid > 0" class="text-xs text-gray-500 mt-1">
                  ₱{{ student.amount_paid.toLocaleString() }}
                </p>
              </div>
            </div>
          </div>
          <div v-if="eligibleStudents.length === 0" class="p-8 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p>No students are eligible for this event based on the selected criteria.</p>
            <p class="text-xs mt-1">Make sure students have the correct department, course, and year level.</p>
          </div>
        </div>
      </div>

      <!-- Guests Tab -->
      <div v-if="activeTab === 'guests'" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Guest Respondents
              <span class="text-sm font-normal text-gray-500">(External participants manually added)</span>
            </h3>
            <div class="flex gap-2">
              <Link 
                :href="`/president/events/${event.id}/guests`"
                class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Manage Guests
              </Link>
            </div>
          </div>
        </div>
        
        <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
          <div v-for="guest in eligibleGuests" :key="guest.id" class="p-4 hover:bg-gray-50 transition">
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <p class="font-medium text-gray-800">{{ guest.name }}</p>
                <p class="text-sm text-gray-500">{{ guest.guest_id }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                  <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">{{ guest.email }}</span>
                  <span v-if="guest.agency_office" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">{{ guest.agency_office }}</span>
                  <span v-if="guest.position" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">{{ guest.position }}</span>
                </div>
              </div>
              <div class="text-right">
                <span :class="[
                  'px-3 py-1 text-xs font-medium rounded-full',
                  guest.status === 'Evaluated' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                ]">
                  {{ guest.status }}
                </span>
                <p class="text-xs text-gray-500 mt-1">
                  Added: {{ formatDate(guest.created_at) }}
                </p>
              </div>
            </div>
          </div>
          <div v-if="!eligibleGuests || eligibleGuests.length === 0" class="p-8 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p>No guest respondents added yet.</p>
            <p class="text-sm mt-1">Click "Manage Guests" to add external participants who can evaluate this event.</p>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-blue-50 rounded-xl p-4">
          <p class="text-sm text-blue-600 font-semibold">Total Eligible Students</p>
          <p class="text-2xl font-bold text-blue-700">{{ stats?.total_students || 0 }}</p>
          <p class="text-xs text-blue-500 mt-1">Based on selected criteria</p>
        </div>
        <div class="bg-indigo-50 rounded-xl p-4">
          <p class="text-sm text-indigo-600 font-semibold">Total Guests</p>
          <p class="text-2xl font-bold text-indigo-700">{{ stats?.total_guests || 0 }}</p>
          <p class="text-xs text-indigo-500 mt-1">Manually added</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4">
          <p class="text-sm text-green-600 font-semibold">Paid</p>
          <p class="text-2xl font-bold text-green-700">{{ stats?.paid || 0 }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4">
          <p class="text-sm text-yellow-600 font-semibold">Pending</p>
          <p class="text-2xl font-bold text-yellow-700">{{ stats?.pending || 0 }}</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4">
          <p class="text-sm text-purple-600 font-semibold">Evaluations</p>
          <p class="text-2xl font-bold text-purple-700">{{ stats?.evaluations || 0 }}</p>
        </div>
      </div>

      <!-- Request Evaluation Modal with INCLUSIVE DATES DISPLAY -->
      <Teleport to="body">
        <div v-if="showRequestModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showRequestModal = false"></div>
          <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
              <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <div class="flex items-center justify-between">
                  <h3 class="text-xl font-semibold text-white">Request Evaluation Service</h3>
                  <button @click="showRequestModal = false" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <form @submit.prevent="submitRequest" class="p-6 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Title of the Activity *</label>
                  <input v-model="requestForm.title" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" required />
                </div>

                <!-- Display Inclusive Dates from Event -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                  <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-semibold text-blue-800">Event Inclusive Dates</span>
                  </div>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <div v-for="(date, idx) in inclusiveDates" :key="date" 
                         class="flex items-center gap-2 p-2 bg-white rounded-lg border border-blue-100">
                      <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-xs font-bold text-blue-700">
                        {{ idx + 1 }}
                      </span>
                      <span class="text-sm text-gray-700">{{ formatDate(date) }}</span>
                    </div>
                  </div>
                  <p class="text-xs text-blue-600 mt-2">
                    These dates are automatically generated from the event's start and end dates.
                    Students will be able to submit separate evaluations for each day.
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Venue *</label>
                  <input v-model="requestForm.venue" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" required />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Name of the Speaker *</label>
                  <input v-model="requestForm.speaker_name" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" required />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Topic/s *</label>
                  <div v-for="(topic, index) in requestForm.topics" :key="index" class="flex gap-2 mb-2">
                    <input v-model="requestForm.topics[index]" type="text" class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" required />
                    <button type="button" @click="removeTopic(index)" v-if="requestForm.topics.length > 1" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                  </div>
                  <button type="button" @click="addTopic" class="text-sm text-purple-600 hover:text-purple-700 mt-1">+ Add Topic</button>
                </div>

                <div>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="requestForm.has_food" class="w-4 h-4 text-purple-600 rounded" />
                    <span class="text-sm text-gray-700">Is there food?</span>
                  </label>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                  <textarea v-model="requestForm.notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                  <button type="button" @click="showRequestModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                  <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700" :disabled="requestForm.processing">
                    {{ requestForm.processing ? 'Submitting...' : 'Submit Request' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </Teleport>

      <!-- Mark as Finished Confirmation Modal -->
      <Teleport to="body">
        <div v-if="showFinishedModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="showFinishedModal = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 mb-4">
                  <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Mark Event as Finished</h3>
                <p class="text-sm text-gray-500 mb-4">
                  Are you sure you want to mark <span class="font-semibold">{{ event.event_name }}</span> as finished?
                </p>
                <p class="text-xs text-gray-400 mb-6">
                  Note: Collections can still continue after marking as finished. This will make the event available for evaluation service requests.
                </p>
                <div class="flex justify-end gap-3">
                  <button @click="showFinishedModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                  <button @click="markAsFinished" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700" :disabled="finishedProcessing">
                    {{ finishedProcessing ? 'Processing...' : 'Mark as Finished' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import axios from 'axios';

const props = defineProps({
  event: {
    type: Object,
    required: true
  },
  departments: {
    type: Array,
    default: () => []
  },
  courses: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  hasEvaluationRequest: {
    type: Boolean,
    default: false
  },
  evaluationRequestStatus: {
    type: String,
    default: ''
  },
  evaluationRequest: {
    type: Object,
    default: null
  },
  eligibleStudents: {
    type: Array,
    default: () => []
  },
  eligibleGuests: {
    type: Array,
    default: () => []
  }
});

const activeTab = ref('students');

// Document upload form
const uploadForm = useForm({
  signed_document: null
});
const documentFileName = ref('');
const showFinishedModal = ref(false);
const finishedProcessing = ref(false);
const showRequestModal = ref(false);
const refreshing = ref(false);
const eligibleStudents = ref(props.eligibleStudents || []);

// Generate inclusive dates from event start and end
const inclusiveDates = ref([]);

// Generate dates when component mounts or when event dates change
function generateInclusiveDates() {
  if (props.event.event_date_start && props.event.event_date_end) {
    const start = new Date(props.event.event_date_start);
    const end = new Date(props.event.event_date_end);
    const dates = [];
    const currentDate = new Date(start);
    
    while (currentDate <= end) {
      dates.push(new Date(currentDate).toISOString().split('T')[0]);
      currentDate.setDate(currentDate.getDate() + 1);
    }
    inclusiveDates.value = dates;
  }
}

// Call on mount
generateInclusiveDates();

const requestForm = useForm({
  title: props.event.event_name,
  venue: '',
  speaker_name: '',
  topics: [''],
  has_food: false,
  notes: ''
});

function handleDocumentFile(e) {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    uploadForm.signed_document = file;
    documentFileName.value = file.name;
  }
}

function handleDocumentDrop(e) {
  const file = e.dataTransfer.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    uploadForm.signed_document = file;
    documentFileName.value = file.name;
  }
}

function uploadDocument() {
  if (!uploadForm.signed_document) {
    alert('Please select a file to upload.');
    return;
  }

  uploadForm.post(`/president/events/${props.event.id}/upload-document`, {
    forceFormData: true,
    onSuccess: () => {
      uploadForm.reset();
      documentFileName.value = '';
      router.reload();
    }
  });
}

function confirmMarkFinished() {
  showFinishedModal.value = true;
}

async function markAsFinished() {
  finishedProcessing.value = true;
  
  try {
    await axios.post(`/president/events/${props.event.id}/mark-finished`);
    showFinishedModal.value = false;
    router.reload();
  } catch (error) {
    console.error('Error marking as finished:', error);
    alert('Failed to mark event as finished. Please try again.');
  } finally {
    finishedProcessing.value = false;
  }
}

async function refreshEligibleStudents() {
  refreshing.value = true;
  try {
    const response = await axios.post(`/president/events/${props.event.id}/refresh-students`);
    if (response.data.success) {
      eligibleStudents.value = response.data.students;
      if (props.stats) {
        props.stats.total_students = response.data.total;
      }
      showToastMessage('Student list refreshed successfully', 'success');
    } else {
      showToastMessage(response.data.error || 'Failed to refresh students', 'error');
    }
  } catch (error) {
    console.error('Error refreshing students:', error);
    showToastMessage(error.response?.data?.error || 'Failed to refresh students', 'error');
  } finally {
    refreshing.value = false;
  }
}

function showToastMessage(message, type = 'success') {
  alert(message);
}

function addTopic() {
  requestForm.topics.push('');
}

function removeTopic(index) {
  requestForm.topics.splice(index, 1);
}

function openRequestModal() {
  requestForm.reset();
  requestForm.title = props.event.event_name;
  requestForm.topics = [''];
  showRequestModal.value = true;
}

function submitRequest() {
  requestForm.post(`/president/events/${props.event.id}/request-evaluation`, {
    onSuccess: () => {
      showRequestModal.value = false;
      router.reload();
    }
  });
}

function getDepartmentName(deptId) {
  if (!props.departments || !Array.isArray(props.departments)) return `Dept ID: ${deptId}`;
  const dept = props.departments.find(d => d.id === deptId);
  return dept ? dept.name : `Dept ID: ${deptId}`;
}

function getCourseName(courseId) {
  if (!props.courses || !Array.isArray(props.courses)) return `Course ID: ${courseId}`;
  const course = props.courses.find(c => c.id === courseId);
  return course ? course.name : `Course ID: ${courseId}`;
}

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function formatApprovalStatus(status) {
  if (!status) return 'N/A';
  return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

function statusBadgeClass(status) {
  const base = 'px-3 py-1 text-xs rounded-full font-medium';
  switch(status) {
    case 'Pending': return `${base} bg-yellow-100 text-yellow-700`;
    case 'Approved': return `${base} bg-green-100 text-green-700`;
    case 'Finished': return `${base} bg-purple-100 text-purple-700`;
    default: return `${base} bg-gray-100 text-gray-700`;
  }
}

function approvalBadgeClass(status) {
  const base = 'px-3 py-1 text-xs rounded-full font-medium';
  switch(status) {
    case 'pending_document': return `${base} bg-yellow-100 text-yellow-700`;
    case 'pending_approval': return `${base} bg-blue-100 text-blue-700`;
    case 'approved': return `${base} bg-green-100 text-green-700`;
    case 'rejected': return `${base} bg-red-100 text-red-700`;
    default: return `${base} bg-gray-100 text-gray-700`;
  }
}
</script>

<style scoped>
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
</style>