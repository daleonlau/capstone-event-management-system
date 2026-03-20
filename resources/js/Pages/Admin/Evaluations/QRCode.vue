<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div class="mb-8">
            <Link :href="`/admin/evaluations/${evaluation.id}`" 
                  class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition mb-4">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Evaluation
            </Link>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Evaluation QR Code
            </h1>
            <p class="text-gray-500 mt-1">{{ evaluation.title }} - {{ evaluation.event_name }}</p>
          </div>
  
          <div class="bg-green-50 border-l-4 border-green-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-sm text-green-800">
                <p class="font-medium">Evaluation Activated Successfully!</p>
                <p>Share this QR code with the organization president so they can disseminate it to students.</p>
              </div>
            </div>
          </div>
  
          <!-- QR Code Display -->
          <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <div class="flex flex-col items-center">
              <!-- QR Code Canvas with direct ID for easy access -->
              <div class="bg-white p-4 rounded-2xl shadow-lg mb-6">
                <canvas 
                  id="qr-canvas-main"
                  width="300" 
                  height="300"
                  style="width: 300px; height: 300px;"
                ></canvas>
              </div>
  
              <div class="w-full max-w-md">
                <label class="block text-sm font-medium text-gray-700 mb-2">Evaluation Link</label>
                <div class="flex gap-2">
                  <input 
                    :id="'evaluation-link'"
                    type="text" 
                    :value="evaluationUrl" 
                    readonly
                    class="flex-1 px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm"
                  />
                  <button 
                    @click="copyLink"
                    class="px-4 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    {{ copied ? 'Copied!' : 'Copy' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Instructions -->
          <div class="bg-blue-50 rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              How to Use
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-blue-600 font-bold">1</span>
                </div>
                <div>
                  <p class="font-medium text-gray-800">Download QR Code</p>
                  <p class="text-sm text-gray-600">Download or print this QR code.</p>
                </div>
              </div>
              
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-blue-600 font-bold">2</span>
                </div>
                <div>
                  <p class="font-medium text-gray-800">Share with Organization</p>
                  <p class="text-sm text-gray-600">Provide the QR code to the organization president.</p>
                </div>
              </div>
              
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-blue-600 font-bold">3</span>
                </div>
                <div>
                  <p class="font-medium text-gray-800">Disseminate to Students</p>
                  <p class="text-sm text-gray-600">The president will share it with students for feedback collection.</p>
                </div>
              </div>
            </div>
  
            <div class="mt-6 flex justify-end gap-3">
              <button 
                @click="downloadQRCode"
                class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download QR Code
              </button>
              <button 
                @click="window.print()"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print QR Code
              </button>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import QRCode from 'qrcode';
  
  const props = defineProps({
    evaluation: {
      type: Object,
      required: true
    },
    qr_data: {
      type: String,
      required: true
    }
  });
  
  const copied = ref(false);
  const evaluationUrl = ref(props.qr_data);
  
  onMounted(() => {
    // Generate QR code when component mounts
    generateQRCode();
  });
  
  async function generateQRCode() {
    try {
      // Get canvas element by ID
      const canvas = document.getElementById('qr-canvas-main');
      
      if (!canvas) {
        console.error('Canvas element not found');
        return;
      }
      
      console.log('Generating QR code for URL:', evaluationUrl.value);
      
      // Clear canvas
      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      // Generate QR code
      await QRCode.toCanvas(canvas, evaluationUrl.value, {
        width: 300,
        margin: 2,
        color: {
          dark: '#000000',
          light: '#ffffff'
        }
      });
      
      console.log('QR Code generated successfully');
      
    } catch (err) {
      console.error('Failed to generate QR code:', err);
    }
  }
  
  async function copyLink() {
    try {
      await navigator.clipboard.writeText(evaluationUrl.value);
      copied.value = true;
      setTimeout(() => {
        copied.value = false;
      }, 2000);
    } catch (err) {
      console.error('Failed to copy:', err);
    }
  }
  
  function downloadQRCode() {
    const canvas = document.getElementById('qr-canvas-main');
    if (canvas) {
      const link = document.createElement('a');
      link.download = `qrcode-${props.evaluation.id}.png`;
      link.href = canvas.toDataURL('image/png');
      link.click();
    } else {
      alert('Unable to download QR code. Please refresh the page and try again.');
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