<template>
    <div>
        <div class="flex items-center">
            <input type="checkbox" :id="id" v-on:change="onChange" :checked="modelValue" class="checkbox opacity-0 absolute"  />
            <label :for="id" class="label mr-2 bg-gray-300 rounded-full cursor-pointer flex items-center justify-between p-2 relative h-8 w-14 transition background duration-200">
                <div class="ball translate-x-0 rounded-full bg-white absolute w-7 h-7 transition transform duration-200"></div>
            </label>
            <label :for="id"><slot></slot></label>
        </div>
    </div>
</template>

<style scoped>
.label .ball {
    top: 2px;
    left: 2px;
}

.checkbox:checked + .label .ball {
    transform: translateX(24px);
}

.checkbox:checked + .label {
    background-color: #524EF2;
}
</style>

<script>
import { nanoid } from 'nanoid'

export default {
    name: "toggle-button",
    props: {
        modelValue: {
            type: Boolean, default: false
        }
    },
    beforeCreate() {
        this.id = nanoid()
    },
    methods: {
        onChange(event) {
            this.$emit("update:modelValue", event.target.checked)
        }
    }
}
</script>
