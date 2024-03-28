<template>
    <div
        class="fixed inset-0 z-[60] bg-gray-500/75 dark:bg-gray-900/75 flex items-center justify-center p-3"
    >
        <div
            class="absolute inset-0"
            @click.prevent="() => emit('close')"
        ></div>
        <div
            class="relative p-3 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow min-w-80 md:w-1/2 max-w-full max-h-full overflow-y-auto scrollbar-hide"
            :class="contentClass"
        >
            <div
                class="absolute top-0 right-0 w-5 cursor-pointer hover:opacity-75 text-lg"
                @click.prevent="() => emit('close')"
            >
                &#x2715;
            </div>

            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        contentClass: string;
    }>(),
    {
        contentClass: '',
    }
);

const emit = defineEmits<{
    (e: "close"): void;
}>();
</script>
