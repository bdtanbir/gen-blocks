<template>
    <div class="templates-page">
        <h1 class="gb-text-2xl gb-font-bold gb-mb-6">Templates</h1>

        <!-- Categories Filter -->
        <div class="gb-flex gb-flex-wrap gb-gap-2 gb-mb-6">
            <button
                @click="templatesStore.clearCategory()"
                :class="[
                    'gb-px-4 gb-py-2 gb-rounded-lg gb-text-sm gb-font-medium gb-transition-colors',
                    !templatesStore.selectedCategory
                        ? 'gb-bg-blue-600 gb-text-white'
                        : 'gb-bg-gray-100 gb-text-gray-700 hover:gb-bg-gray-200'
                ]"
            >
                All Templates
                <span class="gb-ml-1 gb-text-xs gb-opacity-75">({{ templatesStore.templates.length }})</span>
            </button>

            <button
                v-for="category in templatesStore.categories"
                :key="category"
                @click="templatesStore.setCategory(category)"
                :class="[
                    'gb-px-4 gb-py-2 gb-rounded-lg gb-text-sm gb-font-medium gb-transition-colors',
                    templatesStore.selectedCategory === category
                        ? 'gb-bg-blue-600 gb-text-white'
                        : 'gb-bg-gray-100 gb-text-gray-700 hover:gb-bg-gray-200'
                ]"
            >
                {{ category }}
                <span class="gb-ml-1 gb-text-xs gb-opacity-75">({{ templatesStore.categoryCount[category] || 0 }})</span>
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="templatesStore.loading && templatesStore.templates.length === 0" class="gb-flex gb-items-center gb-justify-center gb-py-16">
            <svg class="gb-animate-spin gb-h-8 gb-w-8 gb-text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Empty State -->
        <div v-else-if="templatesStore.filteredTemplates.length === 0" class="gb-bg-white gb-rounded-lg gb-shadow gb-p-12 gb-text-center">
            <svg class="gb-w-16 gb-h-16 gb-mx-auto gb-text-gray-400 gb-mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
            </svg>
            <h3 class="gb-text-lg gb-font-semibold gb-text-gray-900 gb-mb-2">No templates found</h3>
            <p class="gb-text-gray-500">
                {{ templatesStore.selectedCategory ? 'No templates in this category.' : 'No templates available yet.' }}
            </p>
        </div>

        <!-- Templates Grid -->
        <div v-else class="gb-grid gb-grid-cols-1 md:gb-grid-cols-2 lg:gb-grid-cols-3 gb-gap-6">
            <div
                v-for="template in templatesStore.filteredTemplates"
                :key="template.id"
                class="gb-bg-white gb-rounded-lg gb-shadow gb-overflow-hidden hover:gb-shadow-lg gb-transition-shadow"
            >
                <!-- Template Preview -->
                <div class="gb-bg-gray-100 gb-p-6 gb-h-40 gb-flex gb-items-center gb-justify-center">
                    <div class="gb-text-center">
                        <div class="gb-text-4xl gb-mb-2">{{ template.icon || '📄' }}</div>
                        <span class="gb-text-xs gb-text-gray-500 gb-bg-white gb-px-2 gb-py-1 gb-rounded">
                            {{ template.category }}
                        </span>
                    </div>
                </div>

                <!-- Template Info -->
                <div class="gb-p-4">
                    <h3 class="gb-font-semibold gb-text-gray-900 gb-mb-1">{{ template.name }}</h3>
                    <p class="gb-text-sm gb-text-gray-500 gb-mb-4 gb-line-clamp-2">
                        {{ template.description }}
                    </p>

                    <div class="gb-flex gb-items-center gb-justify-between">
                        <span class="gb-text-xs gb-text-gray-400">
                            {{ template.variables?.length || 0 }} variable{{ template.variables?.length !== 1 ? 's' : '' }}
                        </span>
                        <button
                            @click="openPreviewModal(template)"
                            class="gb-px-3 gb-py-1.5 gb-text-sm gb-font-medium gb-text-blue-600 hover:gb-bg-blue-50 gb-rounded-md gb-transition-colors"
                        >
                            Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div
            v-if="showModal"
            class="gb-fixed gb-inset-0 gb-z-50 gb-flex gb-items-center gb-justify-center gb-p-4"
        >
            <div class="gb-absolute gb-inset-0 gb-bg-black gb-bg-opacity-50" @click="closeModal"></div>

            <div class="gb-relative gb-bg-white gb-rounded-lg gb-shadow-xl gb-max-w-2xl gb-w-full gb-max-h-[90vh] gb-overflow-hidden">
                <!-- Modal Header -->
                <div class="gb-flex gb-items-center gb-justify-between gb-p-4 gb-border-b">
                    <h2 class="gb-text-lg gb-font-semibold">{{ selectedTemplate?.name }}</h2>
                    <button
                        @click="closeModal"
                        class="gb-p-1 gb-rounded-full hover:gb-bg-gray-100 gb-transition-colors"
                    >
                        <svg class="gb-w-5 gb-h-5 gb-text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="gb-p-4 gb-overflow-y-auto gb-max-h-[60vh]">
                    <p class="gb-text-gray-600 gb-mb-4">{{ selectedTemplate?.description }}</p>

                    <!-- Variables Form -->
                    <div v-if="selectedTemplate?.variables?.length > 0" class="gb-space-y-4">
                        <h3 class="gb-font-medium gb-text-gray-900">Customize Variables</h3>

                        <div
                            v-for="variable in selectedTemplate.variables"
                            :key="variable.name"
                            class="gb-space-y-1"
                        >
                            <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700">
                                {{ variable.label || variable.name }}
                                <span v-if="variable.required" class="gb-text-red-500">*</span>
                            </label>

                            <textarea
                                v-if="variable.type === 'textarea'"
                                v-model="variableValues[variable.name]"
                                :placeholder="variable.placeholder || variable.default"
                                rows="3"
                                class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                            ></textarea>

                            <select
                                v-else-if="variable.type === 'select'"
                                v-model="variableValues[variable.name]"
                                class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                            >
                                <option v-for="opt in variable.options" :key="opt" :value="opt">
                                    {{ opt }}
                                </option>
                            </select>

                            <input
                                v-else
                                :type="variable.type || 'text'"
                                v-model="variableValues[variable.name]"
                                :placeholder="variable.placeholder || variable.default"
                                class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                            />

                            <p v-if="variable.description" class="gb-text-xs gb-text-gray-500">
                                {{ variable.description }}
                            </p>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div v-if="selectedTemplate?.preview" class="gb-mt-4">
                        <h3 class="gb-font-medium gb-text-gray-900 gb-mb-2">Preview</h3>
                        <div class="gb-bg-gray-100 gb-rounded-lg gb-p-4">
                            <pre class="gb-text-xs gb-text-gray-700 gb-overflow-x-auto">{{ selectedTemplate.preview }}</pre>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="gb-flex gb-items-center gb-justify-end gb-gap-3 gb-p-4 gb-border-t gb-bg-gray-50">
                    <button
                        @click="closeModal"
                        class="gb-px-4 gb-py-2 gb-text-sm gb-font-medium gb-text-gray-700 hover:gb-bg-gray-100 gb-rounded-md gb-transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="applySelectedTemplate"
                        :disabled="templatesStore.applying"
                        class="gb-inline-flex gb-items-center gb-gap-2 gb-px-4 gb-py-2 gb-text-sm gb-font-medium gb-text-white gb-bg-blue-600 hover:gb-bg-blue-700 gb-rounded-md gb-transition-colors disabled:gb-opacity-50"
                    >
                        <svg v-if="templatesStore.applying" class="gb-animate-spin gb-w-4 gb-h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span>{{ templatesStore.applying ? 'Applying...' : 'Apply Template' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div
            v-if="applySuccess"
            class="gb-fixed gb-bottom-4 gb-right-4 gb-bg-green-600 gb-text-white gb-px-4 gb-py-3 gb-rounded-lg gb-shadow-lg gb-flex gb-items-center gb-gap-2"
        >
            <svg class="gb-w-5 gb-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Template applied successfully! Copy the block code from the editor.</span>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useTemplatesStore } from '../stores/templates';

const templatesStore = useTemplatesStore();

const showModal = ref(false);
const selectedTemplate = ref(null);
const variableValues = reactive({});
const applySuccess = ref(false);

onMounted(async () => {
    await templatesStore.loadTemplates();
});

function openPreviewModal(template) {
    selectedTemplate.value = template;

    // Initialize variable values with defaults
    Object.keys(variableValues).forEach(key => delete variableValues[key]);
    if (template.variables) {
        template.variables.forEach(variable => {
            variableValues[variable.name] = variable.default || '';
        });
    }

    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    selectedTemplate.value = null;
}

async function applySelectedTemplate() {
    if (!selectedTemplate.value) return;

    try {
        const result = await templatesStore.applyTemplate(selectedTemplate.value.id, { ...variableValues });

        if (result.success) {
            closeModal();
            applySuccess.value = true;
            setTimeout(() => {
                applySuccess.value = false;
            }, 5000);

            // Copy to clipboard if available
            if (result.serialized && navigator.clipboard) {
                await navigator.clipboard.writeText(result.serialized);
            }
        }
    } catch (error) {
        console.error('Error applying template:', error);
    }
}
</script>
