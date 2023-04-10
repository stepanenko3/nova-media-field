<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div class="image-gallery-field" :class="{ dark: dark }">
                <Gallery :value="field.value" :field="field" :readonly="true" />
            </div>
        </template>
    </PanelItem>
</template>

<script setup>
const props = defineProps({
    index: {
        type: Number,
        required: true,
    },
    resource: {
        type: Object,
        required: true,
    },
    resourceName: {
        type: String,
        required: true,
    },
    resourceId: {
        type: String,
        required: true,
    },
    field: {
        type: Object,
        required: true,
    },
});
</script>
