<template>
  <!-- Modal backdrop -->
  <div v-if="show" class="fixed inset-0 bg-black opacity-90 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">{{ editMode ? 'Edytuj zadanie' : 'Dodaj nowe zadanie' }}</h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 cursor-pointer">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form @submit.prevent="submitTask">
        <div class="mb-4">
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Tytuł <span class="text-red-500">*</span>
          </label>
          <input
              v-model="form.title"
              type="text"
              id="title"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
              maxlength="255"
          />
        </div>

        <div class="mb-4">
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Opis
          </label>
          <textarea
              v-model="form.description"
              id="description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          ></textarea>
        </div>

        <div class="mb-6">
          <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
            Status <span class="text-red-500">*</span>
          </label>
          <select
              v-model="form.status"
              id="status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="pending">W trakcie</option>
            <option value="completed">Ukończone</option>
          </select>
        </div>

        <div class="flex space-x-3">
          <button
              type="submit"
              :disabled="loading"
              class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 cursor-pointer"
          >
            {{ loading ? 'Zapisywanie...' : (editMode ? 'Zaktualizuj' : 'Dodaj zadanie') }}
          </button>
          <button
              type="button"
              @click="closeModal"
              class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 cursor-pointer"
          >
            Anuluj
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const props = defineProps({
  show: Boolean,
  editTask: Object
})

const emit = defineEmits(['close', 'taskSaved'])

const API_URL = import.meta.env.VITE_API_URL
const loading = ref(false)
const editMode = ref(false)

const form = ref({
  title: '',
  description: '',
  status: 'pending'
})

watch(() => props.editTask, (newTask) => {
  if (newTask) {
    editMode.value = true
    form.value = {
      title: newTask.title || newTask.name,
      description: newTask.description || '',
      status: newTask.status
    }
  } else {
    editMode.value = false
    resetForm()
  }
})

const resetForm = () => {
  form.value = {
    title: '',
    description: '',
    status: 'pending'
  }
}

const closeModal = () => {
  resetForm()
  emit('close')
}

const submitTask = async () => {
  loading.value = true

  try {
    if (editMode.value) {
      await axios.put(`${API_URL}/tasks/${props.editTask.id}`, form.value)
    } else {
      await axios.post(`${API_URL}/tasks`, form.value)
    }

    resetForm()
    emit('taskSaved')
    emit('close')
  } catch (error) {
    console.error('Błąd zapisywania zadania:', error)

    if (error.response && error.response.status === 422) {
      const errorData = error.response.data
      let errorMessages = []

      if (errorData.errors) {
        errorMessages = Object.values(errorData.errors).flat()
      } else if (errorData.message) {
        errorMessages = [errorData.message]
      }

      Swal.fire({
        icon: 'error',
        title: 'Błędy walidacji',
        html: errorMessages.map(msg => `• ${msg}`).join('<br>'),
        confirmButtonColor: '#3085d6'
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Błąd!',
        text: 'Wystąpił nieoczekiwany błąd podczas zapisywania zadania',
        confirmButtonColor: '#3085d6'
      })
    }
  } finally {
    loading.value = false
  }
}
</script>