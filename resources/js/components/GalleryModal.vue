<template>
    <Modal :show="value" size="xl">
        <div
            class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
        >
            <div class="overflow-hidden overflow-x-auto relative">
                <table
                    class="w-full divide-y divide-gray-100 dark:divide-gray-700"
                    data-testid="resource-table"
                >
                    <tr>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        ></td>
                        <td
                            class="px-2 py-4 whitespace-nowrap dark:bg-gray-800"
                        >
                            <GalleryPicture
                                :value="value"
                                :field="field"
                                class="w-40 h-40"
                            />
                        </td>
                    </tr>
                    <tr v-if="!value?.markForUpload">
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("ID") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ value.id }}
                        </td>
                    </tr>
                    <tr v-if="value?.markForUpload">
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("File Name") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800 cursor-pointer text-yellow-500"
                        >
                            <b>{{
                                __("This is a new file marked for upload")
                            }}</b>
                        </td>
                    </tr>
                    <tr v-else>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("File Name") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800 cursor-pointer"
                            @click.prevent.stop="
                                () => copyToClipboard(value.file_name)
                            "
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
                    <tr v-if="value?.collection_name">
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("Collection Name") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ value.collection_name }}
                        </td>
                    </tr>
                    <tr v-if="value?.disk">
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("Disk") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ value.disk }}
                        </td>
                    </tr>
                    <tr v-if="value?.conversions_disk">
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("Conversions disk") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ value.conversions_disk }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("File Size") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ humanFileSize(value.size) }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("Mime Type") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ value.mime_type }}
                        </td>
                    </tr>
                    <tr v-if="value?.order_column">
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("Order") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ value.order_column }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("Created at") }}
                        </td>
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ value.created_at }}
                        </td>
                    </tr>
                    <tr v-if="Object.keys(value?.generated_conversions).length">
                        <td
                            class="px-2 py-2 whitespace-nowrap dark:bg-gray-800"
                        >
                            {{ __("Conversions") }}
                        </td>
                        <td class="px-2 whitespace-nowrap dark:bg-gray-800">
                            <div class="flex items-center flex-wrap mb-2">
                                <a
                                    v-for="(
                                        image, conversion
                                    ) of value.generated_conversions"
                                    :key="conversion"
                                    class="flex items-center bg-gray-100 dark:bg-gray-900 mr-2 mt-2 px-2 h-8 rounded-lg hover:opacity-75"
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
            </div>

            <ModalFooter>
                <div class="flex items-center ml-auto">
                    <CancelButton
                        component="button"
                        type="button"
                        dusk="cancel-action-button"
                        class="ml-auto mr-3"
                        @click.prevent="emit('update', null)"
                    />
                </div>
            </ModalFooter>
        </div>
    </Modal>
</template>

<script setup>
import useHumanizeFileSize from "../composables/useHumanizeFileSize";
import useCopyToClipboard from "../composables/useCopyToClipboard";

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

const emit = defineEmits(["update"]);

const { copyToClipboard } = useCopyToClipboard();
const { humanFileSize } = useHumanizeFileSize();
</script>
