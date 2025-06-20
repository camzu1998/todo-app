<template>
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Lista Zadań</h1>
      <button
          @click="showModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 cursor-pointer"
      >
        <i class="fas fa-plus mr-2"></i>Dodaj zadanie
      </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tytuł</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opis</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-center">Akcje</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <tr v-for="task in tasks" :key="task.id" class="hover:bg-gray-50 transition-colors duration-200">
          <td class="px-6 py-4 max-w-xs">
            <div class="truncate" :title="task.title">
              {{ task.title }}
            </div>
          </td>
          <td class="px-6 py-4 max-w-xs">
            <div class="truncate" :title="task.description">
              {{ task.description || '-' }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
              <span :class="task.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                    class="px-2 py-1 text-xs rounded-full">
                {{ getStatusText(task.status) }}
              </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
            <button
                @click="toggleStatus(task)"
                :class="task.status === 'completed' ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800'"
                class="p-2 rounded cursor-pointer"
                :title="task.status === 'completed' ? 'Oznacz jako w trakcie' : 'Oznacz jako ukończone'"
            >
              <i :class="task.status === 'completed' ? 'fas fa-undo' : 'fas fa-check'"></i>
            </button>

            <button
                @click="editTask(task)"
                class="text-blue-600 hover:text-blue-800 p-2 rounded cursor-pointer"
                title="Edytuj zadanie"
            >
              <i class="fas fa-edit"></i>
            </button>

            <button
                @click="confirmDelete(task)"
                class="text-red-600 hover:text-red-800 p-2 rounded cursor-pointer"
                title="Usuń zadanie"
            >
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <TaskForm
        :show="showModal"
        :edit-task="selectedTask"
        @close="closeModal"
        @task-saved="handleTaskSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import TaskForm from './TaskForm.vue'

const API_URL = import.meta.env.VITE_API_URL
const tasks = ref([])
const showModal = ref(false)
const selectedTask = ref(null)

const getStatusText = (status) => {
  return status === 'completed' ? 'Ukończone' : 'W trakcie'
}

const fetchTasks = async () => {
  try {
    const response = await axios.get(`${API_URL}/tasks`)
    tasks.value = response.data.data
  } catch (error) {
    console.error('Błąd pobierania zadań:', error)
    Swal.fire({
      icon: 'error',
      title: 'Błąd!',
      text: 'Nie udało się pobrać listy zadań',
      confirmButtonColor: '#3085d6'
    })
  }
}

const editTask = (task) => {
  selectedTask.value = task
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedTask.value = null
}

const handleTaskSaved = () => {
  fetchTasks()
  Swal.fire({
    icon: 'success',
    title: 'Sukces!',
    text: 'Zadanie zostało zapisane',
    timer: 2000,
    showConfirmButton: false
  })
}

const toggleStatus = async (task) => {
  try {
    const newStatus = task.status === 'completed' ? 'pending' : 'completed'
    await axios.put(`${API_URL}/tasks/${task.id}`, {
      status: newStatus
    })
    await fetchTasks()

    Swal.fire({
      icon: 'success',
      title: 'Status zaktualizowany!',
      text: `Zadanie oznaczono jako ${newStatus === 'completed' ? 'ukończone' : 'w trakcie'}`,
      timer: 2000,
      showConfirmButton: false
    })
  } catch (error) {
    console.error('Błąd aktualizacji zadania:', error)
    Swal.fire({
      icon: 'error',
      title: 'Błąd!',
      text: 'Nie udało się zaktualizować statusu zadania',
      confirmButtonColor: '#3085d6'
    })
  }
}

const confirmDelete = async (task) => {
  const result = await Swal.fire({
    title: 'Czy na pewno?',
    text: `Czy chcesz usunąć zadanie "${task.title || task.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Tak, usuń!',
    cancelButtonText: 'Anuluj'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`${API_URL}/tasks/${task.id}`)
      await fetchTasks()

      Swal.fire({
        icon: 'success',
        title: 'Usunięto!',
        text: 'Zadanie zostało usunięte',
        timer: 2000,
        showConfirmButton: false
      })
    } catch (error) {
      console.error('Błąd usuwania zadania:', error)
      Swal.fire({
        icon: 'error',
        title: 'Błąd!',
        text: 'Nie udało się usunąć zadania',
        confirmButtonColor: '#3085d6'
      })
    }
  }
}

onMounted(() => {
  fetchTasks()
})

defineExpose({ fetchTasks })
</script>