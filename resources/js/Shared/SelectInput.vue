<template>
  <div :class="$attrs.class">
    <label v-if="label" :for="id" class="label">
      <span class="label-text">{{ label }}:</span>
    </label>
    <select :id="id" ref="input" v-model="selected" v-bind="{ ...$attrs, class: null }"
      :class="['select select-bordered w-full', error ? 'select-error' : '']">
      <slot />
    </select>
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
        return `select-input-${uuid()}`
      },
    },
    error: String,
    label: String,
    modelValue: [String, Number, Boolean],
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selected: this.modelValue,
    }
  },
  watch: {
    selected(selected) {
      this.$emit('update:modelValue', selected)
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
  },
}
</script>
