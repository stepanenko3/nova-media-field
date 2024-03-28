<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div class="nova-media-field">
                <Gallery :value="field.value" :field="field" :readonly="true" />
            </div>
        </template>
    </PanelItem>
</template>

<script setup lang="ts">
defineProps<{
    index: number;
    resource: any;
    resourceName: string;
    resourceId: string;
    field: any;
}>();
</script>
