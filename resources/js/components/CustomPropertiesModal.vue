<template>
    <Modal
        :show="true"
        size="3xl"
        @modal-close="handleClose"
        :classWhitelist="[
            'flatpickr-current-month',
            'flatpickr-next-month',
            'flatpickr-prev-month',
            'flatpickr-weekday',
            'flatpickr-weekdays',
            'flatpickr-calendar',
        ]"
    >
        <form
            class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden -action-fields"
            @submit.prevent="handleUpdate"
            autocomplete="off"
        >
            <div
                v-for="field in filledFields"
                :key="field.attribute"
                class="action"
            >
                <component :is="'form-' + field.component" :field="field" :errors="propsErrors" />

                <div v-if="field.error">
                    {{ field.error }}
                </div>
            </div>

            <ModalFooter>
                <div class="flex items-center ml-auto">
                    <CancelButton
                        component="button"
                        type="button"
                        dusk="cancel-action-button"
                        class="ml-auto mr-3"
                        @click.prevent="handleClose"
                    />

                    <DefaultButton type="submit">
                        {{ __("Update") }}
                    </DefaultButton>
                </div>
            </ModalFooter>
        </form>
    </Modal>
</template>

<script setup>
import dot from "dot-object";
import { computed } from "vue";
import { Errors } from 'form-backend-validation'

const props = defineProps({
    value: {
        type: Array,
        required: true,
    },
    fields: {
        type: Array,
        required: true,
    },
    field: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
    },
});

const emit = defineEmits(["close", "input"]);

const propsErrors = computed(() => new Errors(dot.pick(`${props.field.attribute}.${props.value.id}.custom_properties`, props.errors)));

const filledFields = computed(() =>
    props.fields.map((field) => {
        field.value = props.value?.custom_properties
            ? props.value?.custom_properties[field.attribute]
            : null;
        if (field.component.includes("boolean") && /^1|0$/.test(field.value)) {
            field.value = parseInt(field.value);
        }

        return field;
    })
);

function handleClose() {
    emit("close");
}

function handleUpdate() {
    let formData = new FormData();

    props.fields.forEach((field) => field.fill(formData));

    props.value.custom_properties = {};

    for (let [property, value] of formData.entries()) {
        props.value.custom_properties[property] = value;
    }

    emit("input", props.value);
    handleClose();
}
</script>
