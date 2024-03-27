<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div class="image-gallery-field" :class="{ dark: dark }">
                <Gallery :value="field.value" :field="field" :readonly="true" />
            </div>
        </template>
    </PanelItem>
</template>

<script setup lang="ts">
defineProps<{
    index: number;
    resource: object;
    resourceName: string;
    resourceId: string;
    field: object;
}>();
</script>
