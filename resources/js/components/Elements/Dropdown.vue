<template>
    <div class="relative" ref="target">
        <div @click.prevent.stop="() => (state = !state)">
            <slot name="trigger" />
        </div>

        <div
            class="absolute p-1 mt-2 w-48 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 z-20"
            :style="{
                right: 0,
                top: '100%',
            }"
            :class="{
                hidden: !state,
            }"
        >
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { onClickOutside } from "@vueuse/core";

const state = ref(false);
const target = ref();

onClickOutside(target, () => (state.value ? (state.value = false) : null));
</script>
