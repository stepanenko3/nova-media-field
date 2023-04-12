<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <div class="grid gap-4">
                <DropZone
                    v-if="!field.readonly"
                    :multiple="field.multiple"
                    :disabled="field.readonly"
                    :accepted-types="field.acceptedTypes"
                    :dusk="`${field.attribute}-delete-link`"
                    :input-dusk="field.attribute"
                    @file-changed="handleFileChange"
                />

                <Gallery
                    :value="field.value"
                    :field="field"
                    :multiple="field.multiple"
                    :readonly="field.readonly"
                    :errors="errors"
                    @input="field.value = $event"
                />
            </div>
        </template>
    </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";
import uniqid from "uniqid";
import { serialize } from "object-to-formdata";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    data: () => ({
        originalValue: null,
    }),

    mounted() {
        this.originalValue = this.field.value.slice();
    },

    methods: {
        handleFileChange(newFiles) {
            const files = Array.from(newFiles);
            const orderStart =
                this.field.value.length > 0
                    ? Math.max(...this.field.value.map((o) => o.order_column))
                    : 0;

            files.forEach(async (file, index) => {
                const fileId = uniqid();

                const filePayload = {
                    id: fileId,
                    file_name: file.name,
                    mime_type: file.type,
                    size: file.size,
                    order_column: orderStart + 1,
                    created_at: new Date(file.lastModified).toISOString(),
                    updated_at: new Date(file.lastModified).toISOString(),
                    conversion: {
                        original: URL.createObjectURL(file),
                    },
                    generated_conversions: {},
                    markForUpload: true,
                    file: file,
                };

                if (this.field.multiple) {
                    this.field.value.push(filePayload);
                } else {
                    this.field.value = [filePayload];
                }
            });
        },

        getCustomProperties(file) {
            return (this.field.customPropertiesFields || []).reduce(
                (properties, { attribute: property }) => {
                    const value = file?.custom_properties
                        ? file.custom_properties[property]
                        : null;

                    if (value !== null) {
                        properties[property] = value;
                    }

                    return properties;
                },
                {}
            );
        },

        fill(formData) {
            const data = {};

            this.field.value.forEach((file) => {
                const request = {
                    id: file.id,
                    order: file.order_column,
                    custom_properties: this.getCustomProperties(file),
                };

                if (file.markForUpload && !file.error) {
                    request.file = file.file;
                }

                data[request.id] = request;
            });

            this.originalValue.forEach((originalFile) => {
                const index = this.field.value.findIndex(
                    (file) =>
                        originalFile.id === file.id && !file?.markForUpload
                );

                if (index === -1) {
                    data[originalFile.id] = {
                        id: originalFile.id,
                        delete: true,
                    };
                }
            });

            console.log(data);

            serialize(
                {
                    [`${this.field.attribute}`]: data,
                },
                {},
                formData
            );
        },
    },
};
</script>
