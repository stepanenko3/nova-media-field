<template>
    <Modal v-if="value" @close="() => emit('update', null)">
        <GalleryPicture
            :value="value"
            :field="field"
            class="md:hidden w-40 h-40"
        />

        <div class="md:hidden flex items-center flex-wrap mt-2 mb-2">
            <div
                class="flex items-center bg-gray-100 dark:bg-gray-900 mr-2 mt-2 px-2 h-8 cursor-pointer rounded-lg hover:opacity-75"
                @click.prevent.stop="
                    () => copyToClipboard(value.conversion.original)
                "
            >
                {{ __("Copy URL") }}

                <Icon
                    class="ml-2"
                    type="clipboard-copy"
                    width="16"
                    height="16"
                />
            </div>
            <a
                class="flex items-center bg-gray-100 dark:bg-gray-900 mr-2 mt-2 px-2 h-8 cursor-pointer rounded-lg hover:opacity-75"
                :href="value.conversion.original"
                target="_blank"
            >
                {{ __("Original") }}

                <Icon
                    class="ml-2"
                    type="external-link"
                    width="16"
                    height="16"
                />
            </a>
        </div>

        <table
            class="w-full divide-y divide-gray-100 dark:divide-gray-700"
            data-testid="resource-table"
        >
            <tr class="hidden md:table-row ">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800"></td>
                <td class="px-2 py-4 md:whitespace-nowrap dark:bg-gray-800">
                    <GalleryPicture
                        :value="value"
                        :field="field"
                        class="w-40 h-40"
                    />

                    <div class="flex items-center flex-wrap mt-2">
                        <div
                            class="flex items-center bg-gray-100 dark:bg-gray-900 mr-2 mt-2 px-2 h-8 cursor-pointer rounded-lg hover:opacity-75"
                            @click.prevent.stop="
                                () => copyToClipboard(value.conversion.original)
                            "
                        >
                            {{ __("Copy URL") }}

                            <Icon
                                class="ml-2"
                                type="clipboard-copy"
                                width="16"
                                height="16"
                            />
                        </div>
                        <a
                            class="flex items-center bg-gray-100 dark:bg-gray-900 mr-2 mt-2 px-2 h-8 cursor-pointer rounded-lg hover:opacity-75"
                            :href="value.conversion.original"
                            target="_blank"
                        >
                            {{ __("Original") }}

                            <Icon
                                class="ml-2"
                                type="external-link"
                                width="16"
                                height="16"
                            />
                        </a>
                    </div>
                </td>
            </tr>
            <tr v-if="value?.markForUpload">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("File Name") }}
                </td>
                <td
                    class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800 cursor-pointer text-yellow-500"
                >
                    <b>{{ __("This is a new file marked for upload") }}</b>
                </td>
            </tr>
            <tr v-else>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("File Name") }}
                </td>
                <td
                    class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800 cursor-pointer"
                    @click.prevent.stop="() => copyToClipboard(value.file_name)"
                >
                    <b>{{ value.file_name }}</b>

                    <Icon
                        class="ml-2"
                        type="clipboard-copy"
                        width="16"
                        height="16"
                    />
                </td>
            </tr>
            <tr v-if="!value?.markForUpload">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("ID") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ value.id }}
                </td>
            </tr>
            <tr v-if="value?.collection_name">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("Collection Name") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ value.collection_name }}
                </td>
            </tr>
            <tr v-if="value?.disk">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("Disk") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ value.disk }}
                </td>
            </tr>
            <tr v-if="value?.conversions_disk">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("Conversions disk") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ value.conversions_disk }}
                </td>
            </tr>
            <tr>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("File Size") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ humanFileSize(value.size) }}
                </td>
            </tr>
            <tr>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("Mime Type") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ value.mime_type }}
                </td>
            </tr>
            <tr v-if="value?.order_column">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("Order") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ value.order_column }}
                </td>
            </tr>
            <tr>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("Created at") }}
                </td>
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ formatDate(value.created_at) }}
                </td>
            </tr>
            <tr v-if="Object.keys(value?.generated_conversions).length">
                <td class="px-2 py-2 md:whitespace-nowrap dark:bg-gray-800">
                    {{ __("Conversions") }}
                </td>
                <td class="px-2 md:whitespace-nowrap dark:bg-gray-800">
                    <div class="flex items-center flex-wrap mb-2">
                        <a
                            v-for="(
                                image, conversion
                            ) of value.generated_conversions"
                            :key="conversion"
                            class="flex items-center bg-gray-100 dark:bg-gray-900 mr-2 mt-2 px-2 h-8 cursor-pointer rounded-lg hover:opacity-75"
                            :href="image"
                            target="_blank"
                        >
                            {{ conversion }}

                            <Icon
                                class="ml-2"
                                type="external-link"
                                width="16"
                                height="16"
                            />
                        </a>
                    </div>
                </td>
            </tr>
        </table>

        <div class="flex items-center space-x-3 mt-5">
            <Button theme="gray" @click.prevent="() => emit('update', null)">
                {{ __("Cancel") }}
            </Button>
        </div>
    </Modal>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { DateTime } from "luxon";
import useHumanizeFileSize from "../composables/useHumanizeFileSize";
import useCopyToClipboard from "../composables/useCopyToClipboard";
import Button from "./Elements/Button.vue";
import Modal from "./Elements/Modal.vue";

defineProps<{
    value: any;
    field: any;
}>();

const emit = defineEmits<{
    (e: "update", value: any): void;
}>();

const { copyToClipboard } = useCopyToClipboard();
const { humanFileSize } = useHumanizeFileSize();

const timezone = computed(
    () => Nova.config("userTimezone") || Nova.config("timezone")
);

const formatDate = (date) => {
    return DateTime.fromISO(date).setZone(timezone.value).toLocaleString({
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        timeZoneName: "short",
    });
};
</script>
