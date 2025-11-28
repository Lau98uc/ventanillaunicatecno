<template>
  <div :class="$attrs.class">
    <label v-if="label" :for="id" class="label">
      <span class="label-text">{{ label }}:</span>
    </label>
    <input :id="id" ref="input" v-bind="{ ...$attrs, class: null }" :type="type" :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="['input input-bordered w-full', error ? 'input-error' : '']" />
    <p v-if="error" class="text-error text-sm mt-1">{{ error }}</p>
  </div>
</template>


<script>
import { v4 as uuid } from 'uuid'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${uuid()}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    error: String,
    label: String,
    modelValue: String,
  },
  emits: ['update:modelValue'],
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>
