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
    `block px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:rin-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm`
  ]

  if (props.type === 'number') {
    cls.push('w-1/2')
  } else {
    cls.push('w-full')
  }

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

</script>

<template>
  <div>
      <label class="sr-only">{{ label }}</label>
      <p v-if="errorMsg" class="text-red-500 text-sm">{{ errorMsg[0] }}</p>
      <div class="mt-1 flex rounded-md shadow-sm">
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
          <input :type="type"
                 :name="name"
                 :required="required"
                 :value="inputValue"
                 @input="emit('change', $event.target.files[0])"
                 :class="inputClasses"
                 :placeholder="label"/>
        </template>
        <template v-else-if="type === 'select'">
            <select :type="type"
                   :name="name"
                   :required="required"
                   :value="inputValue"
                   @change="emit('update:modelValue', $event.target.value)"
                   :class="inputClasses"
                   :placeholder="label"
                   step="1">
              <option value="">{{ label }}</option>
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
          <input :type="type"
                 :name="name"
                 :required="required"
                 :value="inputValue"
                 @input="emit('update:modelValue', $event.target.value)"
                 :class="inputClasses"
                 :placeholder="label"
                 :min="min"
                 step="1"/>
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
