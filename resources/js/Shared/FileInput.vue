<template>
  <div>
    <label v-if="label" class="label">
      <span class="label-text">{{ label }}:</span>
    </label>

    <div class="input p-0 bg-base-100 border border-base-300" :class="{ 'input-error': errors.length }">
      <input ref="file" type="file" :accept="accept" class="hidden" @change="change" />

      <div v-if="!modelValue" class="p-2">
        <button type="button" class="btn btn-sm btn-neutral" @click="browse">Browse</button>
      </div>

      <div v-else class="flex items-center justify-between w-full p-2">
        <div class="flex-1 text-sm truncate">
          {{ modelValue.name }}
          <span class="text-xs text-base-content/60">({{ filesize(modelValue.size) }})</span>
        </div>
        <button type="button" class="btn btn-sm btn-error" @click="remove">Remove</button>
      </div>
    </div>

    <p v-if="errors.length" class="text-error text-sm mt-1">{{ errors[0] }}</p>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: File,
    label: String,
    accept: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['update:modelValue'],
  watch: {
    modelValue(value) {
      if (!value) {
        this.$refs.file.value = ''
      }
    },
  },
  methods: {
    filesize(size) {
      var i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
    },
    browse() {
      this.$refs.file.click()
    },
    change(e) {
      this.$emit('update:modelValue', e.target.files[0])
    },
    remove() {
      this.$emit('update:modelValue', null)
    },
  },
}
</script>
