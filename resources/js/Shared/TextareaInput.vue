<template>
  <div :class="$attrs.class">
    <label v-if="label" class="label" :for="id">
      <span class="label-text">{{ label }}</span>
    </label>

    <textarea :id="id" ref="input" v-bind="{ ...$attrs, class: null }" class="textarea textarea-bordered w-full"
      :class="{ 'textarea-error': error }" :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)" />

    <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>
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
        return `textarea-input-${uuid()}`
      },
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
  },
}
</script>
