<template>
    <div v-if="value.length > 0">
        <ul ref="galleryWrapper" class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <GalleryItem
                v-for="(image, index) in value"
                :key="`${image.id}-${index}`"
                :value="image"
                :field="field"
                :readonly="readonly"
                :draggable="draggable"
                :errors="errorsData"
                @input="($event) => updateFile($event)"
                @delete="($event) => deleteFile($event)"
                @showDetail="($event) => (detailedMedia = $event)"
                @editCustomProperties="customPropertiesImageIndex = index"
            />
        </ul>

        <GalleryModal
            :value="detailedMedia"
            :field="field"
            @update="($event) => (detailedMedia = $event)"
        />

        <CustomPropertiesModal
            v-if="customPropertiesImageIndex !== null"
            :value="value[customPropertiesImageIndex]"
            :fields="field.customPropertiesFields"
            :field="field"
            :errors="errorsData"
            @input="($event) => updateFile($event)"
            @close="customPropertiesImageIndex = null"
        />
    </div>
</template>

<script setup>
import Sortable from "sortablejs";
import { ref, onMounted, computed } from "vue";
import useDotObject from "../composables/useDotObject";

const props = defineProps({
    value: {
        type: Array,
        required: true,
    },
    field: {
        type: Object,
        required: true,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
    },
});

const emit = defineEmits(["input"]);

const galleryWrapper = ref(null);
const detailedMedia = ref(null);
const customPropertiesImageIndex = ref(null);

const deleteFile = (id) => {
    const index = props.value.findIndex((file) => file.id === id);

    props.value.splice(index, 1);

    emit("input", props.value);
};

const updateFile = (payload) => {
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

const draggable = computed(() => {
    return props.multiple && !props.readonly;
});

onMounted(() => {
    if (draggable.value) {
        Sortable.create(galleryWrapper.value, {
            animation: 150,
            ghostClass: 'opacity-50',
            onSort: (el) => {
                const { oldIndex, newIndex } = el;

                sortHandler(oldIndex, newIndex);
            },
        });
    }
});

function sortHandler(oldIndex, newIndex) {
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

<style>
.gap-4 {
    gap: 1rem;
}

.grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    word-wrap: anywhere;
}

.h-40 {
    height: 160px;
}
.w-40 {
    width: 160px;
}

.w-7 {
    width: 28px;
}
.h-7 {
    height: 28px;
}

.opacity-0 {
    opacity: 0;
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

.transition-all {
    transition-duration: 0.15s;
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.duration-300 {
    transition-duration: 0.3s;
}

.ease-in-out {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
