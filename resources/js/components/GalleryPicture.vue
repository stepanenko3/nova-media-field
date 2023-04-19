<template>
    <img
        v-if="fileType.startsWith('image/')"
        class="object-cover object-center rounded-lg block w-full h-full"
        :src="value.conversion.original"
    />

    <div
        v-else
        class="bg-gray-50 dark:bg-gray-900 rounded-lg flex items-center justify-center text-center text-3xl font-bold w-full h-full"
    >
        {{ fileExtension }}
    </div>
</template>

<script setup>
import mime from "mime";
import { computed } from "vue";

const props = defineProps({
    value: {
        type: Object,
        required: true,
    },
    field: {
        type: Object,
        required: true,
    },
});

const fileType = computed(() => mime.getType(props.value.file_name));
const fileExtension = computed(
    () =>
        /(?:\.([^.]+))?$/.exec(props.value.file_name)[1] ||
        mime.getExtension(fileType.value)
);
</script>

<style>
.object-cover {
    object-fit: cover;
}

.object-center {
    object-position: center;
}
</style>
