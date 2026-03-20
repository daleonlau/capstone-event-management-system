<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Header -->
          <div class="mb-8">
            <Link 
              :href="`/admin/evaluations/${evaluation.id}`" 
              class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition mb-4"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Evaluation
            </Link>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Edit Evaluation Form
            </h1>
            <p class="text-gray-500 mt-1">Modify the evaluation questionnaire (draft only)</p>
          </div>
  
          <!-- Draft Mode Alert -->
          <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-sm text-yellow-800">
                <p class="font-medium">This evaluation is in DRAFT mode.</p>
                <p>You can edit the questionnaire now. QR code generation will be available after activation.</p>
              </div>
            </div>
          </div>
  
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Form Type Selection -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <h2 class="text-lg font-semibold text-gray-800 mb-4">Form Type</h2>
              <select 
                v-model="form.form_type" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
              >
                <option v-for="(name, type) in formTypes" :key="type" :value="type">
                  {{ name }}
                </option>
              </select>
            </div>
  
            <!-- Basic Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
  
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Form Number *</label>
                  <input v-model="form.form_number" type="text" class="w-full px-4 py-2 border rounded-lg" required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Revision *</label>
                  <input v-model="form.revision" type="text" class="w-full px-4 py-2 border rounded-lg" required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date Effectivity *</label>
                  <input v-model="form.date_effectivity" type="text" class="w-full px-4 py-2 border rounded-lg" required />
                </div>
              </div>
  
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Evaluation Title *</label>
                <input v-model="form.title" type="text" class="w-full px-4 py-2 border rounded-lg" required />
              </div>
  
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                  <input v-model="form.available_from" type="datetime-local" class="w-full px-4 py-2 border rounded-lg" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                  <input v-model="form.available_until" type="datetime-local" class="w-full px-4 py-2 border rounded-lg" />
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
  
              <div v-for="(category, catIndex) in form.categories" :key="catIndex" 
                   class="mb-6 p-4 border border-gray-200 rounded-xl relative hover:border-emerald-200 transition">
                <div class="flex justify-between items-start mb-4">
                  <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Category Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="text" 
                      v-model="category.name" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                      required
                    />
                  </div>
                  <button 
                    type="button" 
                    @click="removeCategory(catIndex)" 
                    class="ml-4 text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded-lg transition"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
  
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
  
              <div v-for="(comment, index) in form.comments" :key="index" 
                   class="mb-4 p-4 border border-gray-200 rounded-xl relative">
                <div class="flex items-center gap-3">
                  <input 
                    type="text" 
                    v-model="comment.text" 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                    placeholder="e.g., VIII. Positive Comments"
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
  
            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
              <Link 
                :href="`/admin/evaluations/${evaluation.id}`"
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
                <span>{{ form.processing ? 'Updating...' : 'Update Evaluation' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import { Link, useForm } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  
  const props = defineProps({
    evaluation: {
      type: Object,
      required: true
    },
    formTypes: {
      type: Object,
      default: () => ({})
    },
    event: {
      type: Object,
      default: null
    }
  });
  
  const form = useForm({
    title: props.evaluation.title,
    form_type: props.evaluation.form_type,
    form_number: props.evaluation.form_number,
    revision: props.evaluation.revision,
    date_effectivity: props.evaluation.date_effectivity,
    available_from: props.evaluation.available_from || '',
    available_until: props.evaluation.available_until || '',
    categories: props.evaluation.categories.map(cat => ({
      id: cat.id,
      name: cat.name,
      questions: cat.questions.map(q => ({
        id: q.id,
        text: q.text,
        required: q.required
      }))
    })),
    comments: props.evaluation.comments.map(c => ({
      id: c.id,
      text: c.text,
      required: c.required
    }))
  });
  
  function addCategory() {
    form.categories.push({
      name: '',
      questions: [{ text: '', required: true }]
    });
  }
  
  function removeCategory(index) {
    if (form.categories.length > 1) {
      form.categories.splice(index, 1);
    } else {
      alert('Evaluation must have at least one category.');
    }
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
    form.put(`/admin/evaluations/${props.evaluation.id}`);
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