<template>
  <div class="flex items-center gap-2 text-sm">
    <div class="flex w-full rounded shadow border border-base-300">
      <dropdown :auto-close="false" class="btn btn-sm btn-primary rounded-r-none" placement="bottom-start">
        <template #default>
          <div class="flex items-center gap-1 text-base-primary">
            <span class="hidden md:inline">Filter</span>
            <svg class="w-3 h-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 961.243 599.998">
              <path
                d="M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z" />
            </svg>
          </div>
        </template>

        <template #dropdown>
          <div class="mt-2 px-4 py-6 bg-base-100 text-base-content rounded-box shadow-xl border border-base-300"
            :style="{ maxWidth: `${maxWidth}px` }">
            <slot />
          </div>
        </template>
      </dropdown>

      <input class="input input-sm input-bordered w-full rounded-l-none" autocomplete="off" type="text" name="search"
        placeholder="Search…" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
    </div>

    <button class="btn btn-sm btn-ghost text-base-content" type="button" @click="$emit('reset')">
      Reset
    </button>
  </div>
</template>


<script>
import Dropdown from '@/Shared/Dropdown.vue'

export default {
  components: {
    Dropdown,
  },
  props: {
    modelValue: String,
    maxWidth: {
      type: Number,
      default: 300,
    },
  },
  emits: ['update:modelValue', 'reset'],
}
</script>
