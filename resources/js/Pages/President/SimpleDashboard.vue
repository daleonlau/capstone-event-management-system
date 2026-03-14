<template>
    <div class="min-h-screen bg-gray-100 p-8">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-green-600 mb-4">President Dashboard</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-4">
          <h2 class="text-xl font-semibold mb-4">Welcome!</h2>
          <pre class="bg-gray-100 p-4 rounded">{{ JSON.stringify($page.props, null, 2) }}</pre>
        </div>
        
        <div class="flex gap-4">
          <a href="/president/events" class="bg-blue-600 text-white px-4 py-2 rounded">Events</a>
          <a href="/president/students" class="bg-green-600 text-white px-4 py-2 rounded">Students</a>
          <a href="/logout" class="bg-red-600 text-white px-4 py-2 rounded">Logout</a>
        </div>
      </div>
    </div>
  </template>