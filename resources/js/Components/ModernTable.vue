<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Table Header -->
    <div v-if="title || $slots.header" class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
      <div class="flex items-center justify-between">
        <div>
          <h3 v-if="title" class="text-lg font-semibold text-gray-900">{{ title }}</h3>
          <p v-if="subtitle" class="text-sm text-gray-600 mt-1">{{ subtitle }}</p>
        </div>
        <slot name="header-actions" />
      </div>
    </div>

    <!-- Search and Filters -->
    <div v-if="searchable || $slots.filters" class="px-6 py-4 border-b border-gray-100">
      <div class="flex flex-col sm:flex-row gap-4">
        <!-- Search Input -->
        <div v-if="searchable" class="flex-1">
          <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              v-model="searchQuery"
              type="text"
              :placeholder="searchPlaceholder"
              class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
            />
          </div>
        </div>
        
        <!-- Custom Filters Slot -->
        <slot name="filters" />
      </div>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <!-- Table Headers -->
        <thead class="bg-gray-50">
          <tr>
            <th v-for="column in columns" :key="column.key" 
                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
              {{ column.label }}
            </th>
            <th v-if="hasActions" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="(item, index) in filteredData" :key="item.id || index" 
              class="hover:bg-gray-50 transition-colors duration-150">
            <td v-for="column in columns" :key="column.key" 
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <slot :name="`cell-${column.key}`" :item="item" :value="getColumnValue(item, column.key)">
                {{ getColumnValue(item, column.key) }}
              </slot>
            </td>
            <td v-if="hasActions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <slot name="actions" :item="item" />
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="filteredData.length === 0">
            <td :colspan="columns.length + (hasActions ? 1 : 0)" 
                class="px-6 py-12 text-center text-gray-500">
              <div class="flex flex-col items-center">
                <component :is="emptyIcon" class="w-12 h-12 text-gray-300 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ emptyTitle }}</h3>
                <p class="text-gray-500">{{ emptyMessage }}</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="showPagination" class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing {{ startIndex }} to {{ endIndex }} of {{ totalItems }} results
        </div>
        <div class="flex space-x-2">
          <button 
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
            Previous
          </button>
          <button 
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { MagnifyingGlassIcon, TableCellsIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  subtitle: String,
  data: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    required: true
  },
  searchable: {
    type: Boolean,
    default: true
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...'
  },
  hasActions: {
    type: Boolean,
    default: true
  },
  emptyTitle: {
    type: String,
    default: 'No data found'
  },
  emptyMessage: {
    type: String,
    default: 'There are no items to display at the moment.'
  },
  emptyIcon: {
    type: Object,
    default: () => TableCellsIcon
  },
  showPagination: {
    type: Boolean,
    default: false
  },
  itemsPerPage: {
    type: Number,
    default: 10
  }
})

const searchQuery = ref('')
const currentPage = ref(1)

const filteredData = computed(() => {
  let filtered = props.data

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item => {
      return props.columns.some(column => {
        const value = getColumnValue(item, column.key)
        return String(value).toLowerCase().includes(query)
      })
    })
  }

  if (props.showPagination) {
    const start = (currentPage.value - 1) * props.itemsPerPage
    const end = start + props.itemsPerPage
    filtered = filtered.slice(start, end)
  }

  return filtered
})

const totalItems = computed(() => props.data.length)
const totalPages = computed(() => Math.ceil(totalItems.value / props.itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * props.itemsPerPage + 1)
const endIndex = computed(() => Math.min(currentPage.value * props.itemsPerPage, totalItems.value))

function getColumnValue(item, key) {
  return key.split('.').reduce((obj, k) => obj?.[k], item) ?? '-'
}

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

function previousPage() {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}
</script>