<template>
    <div v-if="value.length > 0" class="nova-media-field">
        <ul ref="galleryWrapper" class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <GalleryItem
                v-for="(image, index) in value"
                :key="`${image.id}-${index}`"
                :value="image"
                :field="field"
                :readonly="readonly"
                :draggable="draggable"
                :errors="errorsData"
                @input="updateFile"
                @delete="deleteFile"
                @showDetail="(e: any) => (detailedMedia = e)"
                @editCustomProperties="customPropertiesImageIndex = index"
            />
        </ul>

        <GalleryModal
            :value="detailedMedia"
            :field="field"
            @update="(e: any) => (detailedMedia = e)"
        />

        <CustomPropertiesModal
            v-if="customPropertiesImageIndex !== null"
            :value="value[customPropertiesImageIndex]"
            :fields="field.customPropertiesFields"
            :field="field"
            :errors="errorsData"
            @input="updateFile"
            @close="customPropertiesImageIndex = null"
        />
    </div>
</template>

<script setup lang="ts">
import Sortable from "sortablejs";
import { ref, onMounted, computed, Ref } from "vue";
import useDotObject from "../composables/useDotObject";

const props = withDefaults(
    defineProps<{
        value: any[];
        field: any;
        readonly?: boolean;
        multiple?: boolean;
        errors: any;
    }>(),
    {
        readonly: false,
        multiple: false,
    }
);

const emit = defineEmits<{
    (e: "input", value: any): void;
}>();

const galleryWrapper: Ref<HTMLElement | null> = ref(null);
const detailedMedia: Ref<any> = ref(null);
const customPropertiesImageIndex: Ref<any | null> = ref(null);

const deleteFile = (id: string | number) => {
    const index = props.value.findIndex((file) => file.id === id);

    props.value.splice(index, 1);

    emit("input", props.value);
};

const updateFile = (payload: any) => {
    const value = props.value.map((file) => {
        if (file.id === payload.id) {
            file = payload;
        }

        return file;
    });

    emit("input", value);
};

const { fromDotCase } = useDotObject();

const errorsData = computed(() => fromDotCase(props?.errors?.errors || {}));

const draggable = computed(() => props.multiple && !props.readonly);

onMounted(() => {
    if (draggable.value && galleryWrapper.value) {
        Sortable.create(galleryWrapper.value, {
            animation: 150,
            ghostClass: "opacity-50",
            onSort: (el) => {
                const { oldIndex, newIndex } = el;

                sortHandler(oldIndex || 0, newIndex || 0);
            },
        });
    }
});

function sortHandler(oldIndex: number, newIndex: number) {
    const item = props.value[oldIndex];
    const images = [...props.value];
    images.splice(oldIndex, 1);
    images.splice(newIndex, 0, item);

    images.forEach((image, index) => {
        image.order_column = index + 1;
    });

    emit("input", images);
}
</script>
