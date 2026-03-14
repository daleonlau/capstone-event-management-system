<template>
    <OrganizationUserLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Header -->
          <div class="mb-8">
            <Link 
              href="/president/evaluations" 
              class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition mb-4"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Evaluations
            </Link>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Create Evaluation Form
            </h1>
            <p class="text-gray-500 mt-1">Design your evaluation questionnaire with categories and questions</p>
          </div>
  
          <!-- Info Alert -->
          <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">About Evaluation Creation:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>The header section (Form Number, Event Details) is fixed in the student view</li>
                  <li>Students must enter their Student ID - system verifies if they're part of the event</li>
                  <li>Department, Course, Year Level dropdowns are automatically filtered by event settings</li>
                  <li>You can add multiple categories with sub-questions (Likert scale 1-5)</li>
                  <li>You can add multiple comment sections for open-ended feedback</li>
                </ul>
              </div>
            </div>
          </div>
  
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </span>
                Basic Information
              </h2>
  
              <div class="space-y-4">
                <!-- Event Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Event <span class="text-red-500">*</span>
                  </label>
                  <select 
                    v-model="form.event_id" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.event_id }"
                    required
                    :disabled="!!props.event"
                  >
                    <option value="">Choose a finished event...</option>
                    <option v-for="event in availableEvents" :key="event.id" :value="event.id">
                      {{ event.event_name }}
                    </option>
                  </select>
                  <p v-if="form.errors.event_id" class="mt-1 text-sm text-red-600">{{ form.errors.event_id }}</p>
                </div>
  
                <!-- Form Header Fields -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Form Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="text" 
                      v-model="form.form_number" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                      placeholder="F-EEF-018d"
                      required
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Revision <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="text" 
                      v-model="form.revision" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                      placeholder="Rev. 0"
                      required
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Date Effectivity <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="text" 
                      v-model="form.date_effectivity" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                      placeholder="04-28-2025"
                      required
                    />
                  </div>
                </div>
  
                <!-- Title -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Evaluation Title <span class="text-red-500">*</span>
                  </label>
                  <input 
                    type="text" 
                    v-model="form.title" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                    placeholder="EVENT EVALUATION FORM"
                    required
                  />
                </div>
  
                <!-- Availability -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Available From
                    </label>
                    <input 
                      type="datetime-local" 
                      v-model="form.available_from"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Available Until
                    </label>
                    <input 
                      type="datetime-local" 
                      v-model="form.available_until"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                    />
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Categories Builder -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Likert Scale Categories</h2>
                <button 
                  type="button" 
                  @click="addCategory"
                  class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Add Category
                </button>
              </div>
  
              <div v-if="form.categories.length === 0" class="text-center py-12 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
                <p>No categories added yet. Click "Add Category" to start building your Likert scale questions.</p>
              </div>
  
              <div v-for="(category, catIndex) in form.categories" :key="catIndex" 
                   class="mb-6 p-4 border border-gray-200 rounded-xl relative hover:border-emerald-200 transition">
                <!-- Category Header -->
                <div class="flex justify-between items-start mb-4">
                  <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Category Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="text" 
                      v-model="category.name" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                      :placeholder="`e.g., I. Information Dissemination`"
                      required
                    />
                  </div>
                  <button 
                    type="button" 
                    @click="removeCategory(catIndex)" 
                    class="ml-4 text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded-lg transition"
                    title="Remove Category"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
  
                <!-- Questions under this category -->
                <div class="ml-4 mt-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-600">Sub-Questions</h3>
                    <button 
                      type="button" 
                      @click="addQuestion(catIndex)"
                      class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      Add Question
                    </button>
                  </div>
  
                  <div v-for="(question, qIndex) in category.questions" :key="qIndex" 
                       class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg">
                    <input 
                      type="text" 
                      v-model="question.text" 
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm"
                      :placeholder="`e.g., a. Timeliness of sending invites`"
                      required
                    />
                    <label class="flex items-center gap-1 text-sm">
                      <input 
                        type="checkbox" 
                        v-model="question.required" 
                        class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                      />
                      Required
                    </label>
                    <button 
                      type="button" 
                      @click="removeQuestion(catIndex, qIndex)"
                      class="text-red-500 hover:text-red-700"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
  
                  <p v-if="category.questions.length === 0" class="text-xs text-gray-400 italic">
                    No questions yet. Click "Add Question" to add sub-questions.
                  </p>
                </div>
              </div>
            </div>
  
            <!-- Comment Sections Builder -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Comment Sections</h2>
                <button 
                  type="button" 
                  @click="addComment"
                  class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Add Comment Section
                </button>
              </div>
  
              <div v-if="form.comments.length === 0" class="text-center py-6 text-gray-500">
                <p>No comment sections added yet. These are optional for open-ended feedback.</p>
              </div>
  
              <div v-for="(comment, index) in form.comments" :key="index" 
                   class="mb-4 p-4 border border-gray-200 rounded-xl relative">
                <div class="flex items-center gap-3">
                  <input 
                    type="text" 
                    v-model="comment.text" 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                    :placeholder="`e.g., VIII. Positive Comments`"
                    required
                  />
                  <label class="flex items-center gap-1 text-sm">
                    <input 
                      type="checkbox" 
                      v-model="comment.required" 
                      class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                    />
                    Required
                  </label>
                  <button 
                    type="button" 
                    @click="removeComment(index)"
                    class="text-red-500 hover:text-red-700"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
  
            <!-- Validation Errors -->
            <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
              <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
              <ul class="list-disc list-inside text-sm text-red-600">
                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
              </ul>
            </div>
  
            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
              <Link 
                href="/president/evaluations"
                class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition"
              >
                Cancel
              </Link>
              <button 
                type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all hover:shadow-lg flex items-center gap-2"
                :disabled="form.processing"
              >
                <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <span>{{ form.processing ? 'Creating...' : 'Create Evaluation' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { Link, useForm, usePage } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  
  const props = defineProps({
    event: {
      type: Object,
      default: null
    }
  });
  
  // Get available events from page props
  const page = usePage();
  const availableEvents = computed(() => {
    if (props.event) {
      return [props.event];
    }
    return page.props.availableEvents || [];
  });
  
  // Initialize form
  const form = useForm({
    event_id: props.event?.id || '',
    title: 'EVENT EVALUATION FORM',
    form_number: 'F-EEF-018d',
    revision: 'Rev. 0',
    date_effectivity: '04-28-2025',
    available_from: '',
    available_until: '',
    categories: [],
    comments: []
  });
  
  // Add a default category with sample questions
  function addCategory() {
    form.categories.push({
      name: '',
      questions: [{ text: '', required: true }]
    });
  }
  
  function removeCategory(index) {
    form.categories.splice(index, 1);
  }
  
  function addQuestion(categoryIndex) {
    form.categories[categoryIndex].questions.push({ text: '', required: true });
  }
  
  function removeQuestion(categoryIndex, questionIndex) {
    if (form.categories[categoryIndex].questions.length > 1) {
      form.categories[categoryIndex].questions.splice(questionIndex, 1);
    } else {
      alert('Category must have at least one question.');
    }
  }
  
  function addComment() {
    form.comments.push({ text: '', required: false });
  }
  
  function removeComment(index) {
    form.comments.splice(index, 1);
  }
  
  function submit() {
    // Validate at least one category
    if (form.categories.length === 0) {
      form.errors.categories = 'Please add at least one category.';
      return;
    }
  
    // Validate each category has a name and questions
    for (let i = 0; i < form.categories.length; i++) {
      if (!form.categories[i].name.trim()) {
        form.errors[`categories.${i}.name`] = 'Category name is required.';
        return;
      }
      
      if (form.categories[i].questions.length === 0) {
        form.errors[`categories.${i}.questions`] = 'Category must have at least one question.';
        return;
      }
      
      for (let j = 0; j < form.categories[i].questions.length; j++) {
        if (!form.categories[i].questions[j].text.trim()) {
          form.errors[`categories.${i}.questions.${j}.text`] = 'Question text is required.';
          return;
        }
      }
    }
  
    form.post('/president/evaluations');
  }
  
  // Add initial category if none
  if (form.categories.length === 0) {
    addCategory();
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