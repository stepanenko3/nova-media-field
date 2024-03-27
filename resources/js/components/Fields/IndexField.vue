<template>
    <div
        class="flex items-center justify-start"
        :style="{
            width: `${width}px`,
        }"
    >
        <template v-if="value.length > 0">
            <img
                v-for="(image, index) of displayed"
                :src="image.conversion.original"
                class="object-cover object-center rounded-lg block w-8 h-8 border-2 border-solid border-white dark:border-gray-800"
                :class="{
                    '-ml-4': value.length > 0 && index > 0,
                }"
            />
            <div
                v-if="value.length > displayed.length"
                class="flex-shrink-0 bg-primary-500 text-white text-xs leading-none font-semibold inline-flex items-center justify-center w-8 h-8 rounded-lg border-2 border-solid border-white dark:border-gray-800 -ml-4"
            >
                +{{ value.length - displayed.length }}
            </div>
        </template>
        <template v-else> - </template>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
    resourceName: string;
    field: object;
}>();

const value = computed(() => props.field.displayedAs || props.field.value);

const displayed = computed(() => {
    if (value.value.length <= props.field.countOfImagesDisplayedOnIndex + 1) {
        return value.value;
    }

    return value.value.slice(0, props.field.countOfImagesDisplayedOnIndex);
});

const width = computed(() => {
    const count =
        value.value.length > displayed.value.length
            ? displayed.value.length + 1
            : displayed.value.length;

    return count * 32 - (count - 1) * 16;
});
</script>
