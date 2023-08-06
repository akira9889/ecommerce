<script setup>
import { ref, watchEffect, computed } from 'vue';

const props = defineProps({
  modelValue: [String, Number, File],
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  name: String,
  required: Boolean,
  prepend: {
    type: String,
    default: ''
  },
  append: {
    type: String,
    default: ''
  },
  min: Number,
  selectOptions: Array,
  errorMsg: Object
})

const id = computed(() => {
  if (props.id) return props.id;
  return `id-${Math.floor(1000000 + Math.random() * 1000000)}`;
})

const inputValue = ref(props.modelValue);

watchEffect(() => {
  inputValue.value = props.modelValue;
});

const inputClasses = computed(() => {
  const cls = [
    `block px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:rin-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm w-full`
  ]

  if (props.append && !props.prepend) {
    cls.push(`rounded-l-md`)
  } else if (props.prepend && !props.append) {
    cls.push(`rounded-r-md`)
  } else if (!props.prepend && !props.append) {
    cls.push('rounded-md')
  }

  return cls.join(' ')
})

const emit = defineEmits(['update:modelValue', 'change'])

function onChange(value) {
  emit('update:modelValue', value)
  emit('change', value)
}
</script>

<template>
  <div class="mt-2">
      <p v-if="errorMsg" class="text-red-500 text-sm leading-4">{{ errorMsg[0]}}</p>
      <div class="flex rounded-md relative mt-5" :class="{'shadow-sm': !['checkbox', 'file'].includes(type) }">
        <label v-if="inputValue && type !== 'checkbox'" :class="[inputValue ? 'block' : 'hidden', 'absolute text-xs leading-4 text-gray-900 top-0 left-0 -translate-y-[110%]']">{{ label }}</label>
        <span v-if="prepend"
              class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-200 text-gray-500 text-sm">
          {{ prepend }}
        </span>
        <template v-if="type === 'textarea'">
        <textarea :name="name"
                  :required="required"
                  :value="inputValue"
                  @input="emit('update:modelValue', $event.target.value)"
                  :class="inputClasses"
                  :placeholder="label"></textarea>
        </template>
        <template v-else-if="type === 'file'">
          <label class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
            <input :type="type"
                   :name="name"
                   :required="required"
                   :value="inputValue"
                   @input="emit('change', $event.target.files[0])"
                   class="hidden"
                   :placeholder="label"/>
            画像を選択
          </label>
        </template>
        <template v-else-if="type === 'select'">
            <select :type="type"
                   :name="name"
                   :required="required"
                   :value="inputValue"
                   @change="onChange($event.target.value)"
                   :class="inputClasses"
                   :placeholder="label"
                   step="1">
              <option v-if="label" value="">{{ label }}</option>
              <option v-for="option of selectOptions" :value="option.key" :key="option.key">{{ option.text }}</option>
            </select>
        </template>
        <template v-else-if="type === 'checkbox'">
          <div class="flex items-center">
            <input :id="id" :type="type"
            :name="name"
            :required="required"
            :checked="inputValue"
            @change="emit('update:modelValue', $event.target.checked)"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label :for="id" class="block text-sm text-gray-9000 ml-3"> {{ label }}</label>
          </div>
        </template>
        <template v-else>
          <div :class="[type === 'number' ? 'w-1/2' : 'w-full', 'relative']">
            <input :type="type"
                  :name="name"
                  :required="required"
                  :value="inputValue"
                  @input="emit('update:modelValue', $event.target.value)"
                  :class="inputClasses"
                  :min="min"
                  step="1"
                  :placeholder="label"
                  autocomplete
            />
        </div>
        </template>
        <span v-if="append"
              class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-200 text-gray-500 text-sm">
          {{ append }}
        </span>
      </div>
    </div>
</template>

<style scoped>

</style>
