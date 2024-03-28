<template>
    <a
        v-if="'external' === as"
        class="flex items-center cursor-pointer px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800"
        :target="target"
        :href="href"
    >
        <slot />
    </a>
    <div
        v-else
        class="flex items-center cursor-pointer px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800"
    >
        <slot />
    </div>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        as?: string;
        target?: string;
        href?: string;
    }>(),
    {
        as: "button",
        target: "_blank",
        href: '',
    }
);
</script>
