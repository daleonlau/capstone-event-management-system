<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-2xl shadow-lg p-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600 mx-auto mb-4"></div>
      <p class="text-gray-600">Loading response data...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 rounded-2xl shadow-lg p-6">
      <div class="flex items-center gap-3 text-red-800">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <p class="font-medium">{{ error }}</p>
      </div>
    </div>

    <!-- Raw Data Display -->
    <div v-else-if="responses && responses.length > 0" class="space-y-6">
      <!-- Header with Stats and Export -->
      <div class="bg-white rounded-2xl shadow-lg p-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Respondent Responses (Raw Data)
            </h3>
            <p class="text-xs text-gray-500 mt-1">
              Total of <span class="font-semibold text-emerald-600">{{ responses.length }}</span> responses collected
            </p>
          </div>
          <div class="flex gap-2">
            <button 
              @click="exportToCSV" 
              class="px-3 py-1.5 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-1"
            >
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Export CSV
            </button>
            <button 
              @click="copyToClipboard" 
              class="px-3 py-1.5 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-1"
            >
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
              </svg>
              {{ copied ? 'Copied!' : 'Copy' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Search and Filter Bar -->
      <div class="bg-white rounded-2xl shadow-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <!-- Search Input -->
          <div class="md:col-span-2">
            <div class="relative">
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Search by name, ID, email, comments..."
                class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-emerald-500 focus:border-emerald-500 pl-8"
              >
              <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <button 
                v-if="searchQuery" 
                @click="searchQuery = ''" 
                class="absolute right-2.5 top-2 text-gray-400 hover:text-gray-600"
              >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Respondent Type Filter -->
          <div>
            <select 
              v-model="selectedRespondentType" 
              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
            >
              <option value="">All Types</option>
              <option value="Student">Student</option>
              <option value="Faculty">Faculty</option>
              <option value="Admin Personnel">Admin Personnel</option>
              <option value="Guest">Guest</option>
            </select>
          </div>

          <!-- Sex Filter -->
          <div>
            <select 
              v-model="selectedSex" 
              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
            >
              <option value="">All Genders</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Prefer not to say">Prefer not to say</option>
            </select>
          </div>
        </div>

        <!-- Second Row - Additional Filters -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
          <!-- Date Filter -->
          <div>
            <select 
              v-model="selectedEventDate" 
              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
            >
              <option value="">All Dates</option>
              <option v-for="date in eventDateOptions" :key="date" :value="date">
                {{ formatDateShort(date) }}
              </option>
            </select>
          </div>

          <!-- Rating Filter -->
          <div>
            <select v-model="ratingFilter" class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
              <option value="">All Ratings</option>
              <option value="5">5 Stars Only</option>
              <option value="4">4 Stars Only</option>
              <option value="3">3 Stars Only</option>
              <option value="2">2 Stars Only</option>
              <option value="1">1 Star Only</option>
            </select>
          </div>

          <!-- Has Comments Filter -->
          <div>
            <select v-model="hasCommentsFilter" class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
              <option value="">All Responses</option>
              <option value="with">With Comments Only</option>
              <option value="without">Without Comments</option>
            </select>
          </div>

          <!-- Date Range Filter -->
          <div>
            <select v-model="dateRange" class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
              <option value="">All Time</option>
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
            </select>
          </div>
        </div>

        <!-- Active Filters Display -->
        <div v-if="hasActiveFilters" class="mt-3 flex flex-wrap gap-1.5 items-center">
          <span class="text-xs text-gray-500">Filters:</span>
          <span v-if="searchQuery" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
            Search: {{ searchQuery }}
            <button @click="searchQuery = ''" class="ml-1 hover:text-emerald-800">&times;</button>
          </span>
          <span v-if="selectedRespondentType" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
            Type: {{ selectedRespondentType }}
            <button @click="selectedRespondentType = ''" class="ml-1 hover:text-emerald-800">&times;</button>
          </span>
          <span v-if="selectedSex" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
            Gender: {{ selectedSex }}
            <button @click="selectedSex = ''" class="ml-1 hover:text-emerald-800">&times;</button>
          </span>
          <span v-if="selectedEventDate" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
            Date: {{ formatDateShort(selectedEventDate) }}
            <button @click="selectedEventDate = ''" class="ml-1 hover:text-emerald-800">&times;</button>
          </span>
          <span v-if="ratingFilter" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
            Rating: {{ ratingFilter }}★
            <button @click="ratingFilter = ''" class="ml-1 hover:text-emerald-800">&times;</button>
          </span>
          <span v-if="hasCommentsFilter" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
            {{ hasCommentsFilter === 'with' ? 'With Comments' : 'Without Comments' }}
            <button @click="hasCommentsFilter = ''" class="ml-1 hover:text-emerald-800">&times;</button>
          </span>
          <span v-if="dateRange" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
            {{ getDateRangeLabel() }}
            <button @click="dateRange = ''" class="ml-1 hover:text-emerald-800">&times;</button>
          </span>
          <button 
            @click="clearAllFilters" 
            class="text-xs text-red-500 hover:text-red-700 flex items-center gap-0.5"
          >
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Clear all
          </button>
        </div>

        <!-- Filter Stats -->
        <div class="mt-2 text-xs text-gray-500">
          Showing {{ filteredResponses.length }} of {{ responses.length }} responses
        </div>
      </div>

      <!-- Table View with Pagination -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
               <tr>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">#</th>
                <th v-for="header in visibleHeaders" :key="header" 
                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                    :class="{ 'sticky left-12 bg-gray-50 z-10': header === 'Student Name' || header === 'Student ID' }"
                    @click="sortBy(header)">
                  {{ header }}
                  <span v-if="sortColumn === header" class="ml-1 text-xs">
                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                  </span>
                </th>
               </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="(response, idx) in paginatedFilteredResponses" :key="idx" class="hover:bg-gray-50 transition">
                <td class="px-3 py-2 text-xs text-gray-500 sticky left-0 bg-white z-10">{{ (currentPage - 1) * itemsPerPage + idx + 1 }}</td>
                <td v-for="header in visibleHeaders" :key="header" 
                    class="px-3 py-2 text-xs"
                    :class="{ 
                      'sticky left-12 bg-white z-10': header === 'Student Name' || header === 'Student ID',
                      'bg-green-50': header.includes('Rating') && response[header] >= 4,
                      'bg-yellow-50': header.includes('Rating') && response[header] === 3,
                      'bg-red-50': header.includes('Rating') && response[header] <= 2
                    }">
                  <div v-if="header.includes('Rating') && typeof response[header] === 'number'" class="flex items-center gap-1">
                    <span class="font-bold" :class="{
                      'text-green-700': response[header] >= 4,
                      'text-yellow-700': response[header] === 3,
                      'text-red-700': response[header] <= 2
                    }">{{ response[header] }}</span>
                    <span class="text-gray-400 text-xs">★</span>
                  </div>
                  <div v-else-if="header.includes('Comment') || header === 'Positive Comments' || header === 'Suggestions/Recommendations for Improvement'" class="max-w-[300px]">
                    <div class="truncate" :title="response[header]">{{ response[header] || '—' }}</div>
                  </div>
                  <div v-else>
                    {{ formatCellValue(response[header]) }}
                  </div>
                 </td>
               </tr>
              <tr v-if="filteredResponses.length === 0">
                <td :colspan="visibleHeaders.length + 1" class="px-3 py-8 text-center text-xs text-gray-500">
                  No responses match your filters
                 </td>
               </tr>
            </tbody>
           </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="filteredResponses.length > 0" class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs">
          <div class="text-gray-500">
            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredResponses.length) }} of {{ filteredResponses.length }}
          </div>
          <div class="flex items-center gap-2">
            <button 
              @click="currentPage--" 
              :disabled="currentPage === 1"
              class="px-2 py-1 text-xs bg-white border rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
              Prev
            </button>
            <div class="flex gap-1">
              <button 
                v-for="page in visiblePages" 
                :key="page"
                @click="currentPage = page"
                :class="[
                  'px-2 py-1 text-xs rounded-md transition',
                  currentPage === page 
                    ? 'bg-emerald-600 text-white' 
                    : 'bg-white border hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              <span v-if="totalPages > 5 && currentPage < totalPages - 2" class="px-1 py-1 text-xs">...</span>
              <button 
                v-if="totalPages > 5 && currentPage < totalPages - 2"
                @click="currentPage = totalPages"
                class="px-2 py-1 text-xs bg-white border rounded-md hover:bg-gray-50 transition"
              >
                {{ totalPages }}
              </button>
            </div>
            <button 
              @click="currentPage++" 
              :disabled="currentPage === totalPages"
              class="px-2 py-1 text-xs bg-white border rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
              Next
            </button>
            <select 
              v-model="itemsPerPage" 
              class="px-1 py-1 text-xs border rounded-md bg-white"
              @change="currentPage = 1"
            >
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl shadow-lg p-3">
          <p class="text-xs text-gray-500">Total</p>
          <p class="text-xl font-bold text-gray-800">{{ filteredResponses.length }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3">
          <p class="text-xs text-gray-500">Students</p>
          <p class="text-xl font-bold text-gray-800">{{ uniqueStudentCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3">
          <p class="text-xs text-gray-500">Guests</p>
          <p class="text-xl font-bold text-gray-800">{{ guestCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3">
          <p class="text-xs text-gray-500">F/M</p>
          <p class="text-base font-bold text-gray-800">{{ femaleCount }} / {{ maleCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3">
          <p class="text-xs text-gray-500">Avg Rating</p>
          <p class="text-xl font-bold text-emerald-600">{{ averageRating.toFixed(1) }}</p>
        </div>
      </div>
    </div>

    <!-- No Data State -->
    <div v-else class="bg-white rounded-2xl shadow-lg p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No Responses Yet</h3>
      <p class="text-gray-500">No responses have been submitted for this evaluation yet.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  evaluationId: {
    type: Number,
    required: true
  },
  evaluation: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(true);
const error = ref(null);
const responses = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(25);
const copied = ref(false);

// Filter states
const searchQuery = ref('');
const selectedRespondentType = ref('');
const selectedSex = ref('');
const selectedEventDate = ref('');
const dateRange = ref('');
const ratingFilter = ref('');
const hasCommentsFilter = ref('');

// Sort states
const sortColumn = ref('');
const sortDirection = ref('asc');

// Get event date options from responses
const eventDateOptions = computed(() => {
  const dates = new Set(responses.value.map(r => r['Event Date']).filter(d => d && d !== 'N/A'));
  return Array.from(dates).sort();
});

// Visible headers - includes all columns from the response
const visibleHeaders = computed(() => {
  if (!responses.value.length) return [];
  const firstResponse = responses.value[0];
  // Order headers: profile fields first, then ratings, then comments
  const profileHeaders = ['Student ID', 'Student Name', 'Email', 'Event Date', 'Agency/Office', 'Position', 'Respondent Type', 'Sex', 'Age', 'Title', 'Submitted At'];
  const otherHeaders = Object.keys(firstResponse).filter(h => !profileHeaders.includes(h));
  return [...profileHeaders.filter(h => firstResponse[h] !== undefined), ...otherHeaders];
});

// Calculate average rating across all responses
const averageRating = computed(() => {
  let total = 0;
  let count = 0;
  responses.value.forEach(response => {
    Object.keys(response).forEach(key => {
      if (key.includes('Rating') && typeof response[key] === 'number') {
        total += response[key];
        count++;
      }
    });
  });
  return count > 0 ? total / count : 0;
});

// Check if a response has any comments
const hasComments = (response) => {
  return Object.keys(response).some(key => {
    if ((key.includes('Comment') || key === 'Positive Comments' || key === 'Suggestions/Recommendations for Improvement') && 
        response[key] && response[key] !== '—' && response[key] !== '') {
      return true;
    }
    return false;
  });
};

// Filtered responses
const filteredResponses = computed(() => {
  let filtered = [...responses.value];
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(r => {
      return Object.values(r).some(value => 
        value && String(value).toLowerCase().includes(query)
      );
    });
  }
  
  if (selectedRespondentType.value) {
    filtered = filtered.filter(r => r['Respondent Type'] === selectedRespondentType.value);
  }
  
  if (selectedSex.value) {
    filtered = filtered.filter(r => r['Sex'] === selectedSex.value);
  }
  
  if (selectedEventDate.value) {
    filtered = filtered.filter(r => r['Event Date'] === selectedEventDate.value);
  }
  
  if (dateRange.value) {
    const now = new Date();
    filtered = filtered.filter(r => {
      const submittedDate = new Date(r['Submitted At']);
      if (dateRange.value === 'today') {
        return submittedDate.toDateString() === now.toDateString();
      } else if (dateRange.value === 'week') {
        const weekAgo = new Date(now.setDate(now.getDate() - 7));
        return submittedDate >= weekAgo;
      } else if (dateRange.value === 'month') {
        const monthAgo = new Date(now.setMonth(now.getMonth() - 1));
        return submittedDate >= monthAgo;
      }
      return true;
    });
  }
  
  // Rating filter
  if (ratingFilter.value) {
    const targetRating = parseInt(ratingFilter.value);
    filtered = filtered.filter(r => {
      return Object.keys(r).some(key => {
        if (key.includes('Rating') && r[key] === targetRating) {
          return true;
        }
        return false;
      });
    });
  }
  
  // Has comments filter
  if (hasCommentsFilter.value === 'with') {
    filtered = filtered.filter(r => hasComments(r));
  } else if (hasCommentsFilter.value === 'without') {
    filtered = filtered.filter(r => !hasComments(r));
  }
  
  if (sortColumn.value) {
    filtered.sort((a, b) => {
      let aVal = a[sortColumn.value] || '';
      let bVal = b[sortColumn.value] || '';
      
      if (typeof aVal === 'number' && typeof bVal === 'number') {
        return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal;
      }
      
      aVal = String(aVal).toLowerCase();
      bVal = String(bVal).toLowerCase();
      
      if (sortDirection.value === 'asc') {
        return aVal.localeCompare(bVal);
      } else {
        return bVal.localeCompare(aVal);
      }
    });
  }
  
  return filtered;
});

const totalPages = computed(() => Math.ceil(filteredResponses.value.length / itemsPerPage.value));

const paginatedFilteredResponses = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredResponses.value.slice(start, end);
});

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});

const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedRespondentType.value || selectedSex.value || 
         selectedEventDate.value || dateRange.value || ratingFilter.value || hasCommentsFilter.value;
});

const uniqueStudentCount = computed(() => {
  const studentIds = new Set(filteredResponses.value.map(r => r['Student ID']).filter(id => id && id !== 'N/A'));
  return studentIds.size;
});

const guestCount = computed(() => {
  return filteredResponses.value.filter(r => r['Respondent Type'] === 'Guest').length;
});

const femaleCount = computed(() => {
  return filteredResponses.value.filter(r => r['Sex'] === 'Female').length;
});

const maleCount = computed(() => {
  return filteredResponses.value.filter(r => r['Sex'] === 'Male').length;
});

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric'
  });
}

function formatDateShort(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short', day: 'numeric'
  });
}

function formatCellValue(value) {
  if (value === null || value === undefined) return '—';
  if (typeof value === 'object') return JSON.stringify(value);
  if (String(value).length > 100) return String(value).substring(0, 97) + '...';
  return String(value);
}

function getDateRangeLabel() {
  const labels = {
    today: 'Today',
    week: 'This Week',
    month: 'This Month'
  };
  return labels[dateRange.value] || dateRange.value;
}

function clearAllFilters() {
  searchQuery.value = '';
  selectedRespondentType.value = '';
  selectedSex.value = '';
  selectedEventDate.value = '';
  dateRange.value = '';
  ratingFilter.value = '';
  hasCommentsFilter.value = '';
}

function sortBy(column) {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column;
    sortDirection.value = 'asc';
  }
}

async function fetchRawResponses() {
  loading.value = true;
  error.value = null;
  
  try {
    const params = {};
    if (selectedEventDate.value) {
      params.event_date = selectedEventDate.value;
    }
    const response = await axios.get(`/admin/evaluations/${props.evaluationId}/raw-responses`, { params });
    responses.value = response.data;
    currentPage.value = 1;
  } catch (err) {
    console.error('Failed to fetch raw responses:', err);
    error.value = err.response?.data?.message || 'Failed to load response data';
  } finally {
    loading.value = false;
  }
}

function exportToCSV() {
  if (!filteredResponses.value.length) return;
  
  const headers = visibleHeaders.value;
  const rows = filteredResponses.value.map(row => 
    headers.map(header => {
      let value = row[header] || '';
      if (typeof value === 'string' && (value.includes(',') || value.includes('"') || value.includes('\n'))) {
        value = '"' + value.replace(/"/g, '""') + '"';
      }
      return value;
    }).join(',')
  );
  
  const csvContent = [headers.join(','), ...rows].join('\n');
  const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);
  link.href = url;
  link.download = `${props.evaluation.title || 'evaluation'}_responses_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

async function copyToClipboard() {
  if (!filteredResponses.value.length) return;
  
  const headers = visibleHeaders.value;
  const rows = filteredResponses.value.map(row => 
    headers.map(header => row[header] || '').join('\t')
  );
  const text = [headers.join('\t'), ...rows].join('\n');
  
  try {
    await navigator.clipboard.writeText(text);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
}

const resetPage = () => {
  currentPage.value = 1;
};

watch([searchQuery, selectedRespondentType, selectedSex, selectedEventDate, dateRange, ratingFilter, hasCommentsFilter], () => {
  resetPage();
  fetchRawResponses();
});

onMounted(() => {
  fetchRawResponses();
});

defineExpose({ refresh: fetchRawResponses });
</script>

<style scoped>
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}

.sticky {
  position: sticky;
  background-color: white;
  z-index: 10;
}

.sticky-left-0 {
  left: 0;
}

.sticky-left-12 {
  left: 48px;
}
</style>