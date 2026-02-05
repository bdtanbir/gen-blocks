<template>
    <div class="settings-page">
        <h1 class="gb-text-2xl gb-font-bold gb-mb-6">Settings</h1>

        <!-- Loading State -->
        <div v-if="settingsStore.loading" class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
            <div class="gb-flex gb-items-center gb-justify-center gb-py-8">
                <svg class="gb-animate-spin gb-h-8 gb-w-8 gb-text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="gb-ml-3 gb-text-gray-600">Loading settings...</span>
            </div>
        </div>

        <form v-else @submit.prevent="saveSettings" class="gb-space-y-6">
            <!-- API Configuration -->
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <h2 class="gb-text-lg gb-font-semibold gb-mb-4">API Configuration</h2>

                <div class="gb-space-y-4">
                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            API Provider
                        </label>
                        <select
                            v-model="form.api_provider"
                            class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                        >
                            <option value="openai">OpenAI</option>
                            <option value="anthropic">Anthropic</option>
                        </select>
                    </div>

                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            API Key
                        </label>
                        <div class="gb-flex gb-gap-2">
                            <input
                                :type="showApiKey ? 'text' : 'password'"
                                v-model="form.api_key"
                                class="gb-flex-1 gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                                placeholder="Enter your API key"
                            />
                            <button
                                type="button"
                                @click="showApiKey = !showApiKey"
                                class="gb-px-3 gb-py-2 gb-border gb-border-gray-300 gb-rounded-md hover:gb-bg-gray-50"
                            >
                                <svg v-if="showApiKey" class="gb-w-5 gb-h-5 gb-text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                                <svg v-else class="gb-w-5 gb-h-5 gb-text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            Model
                        </label>
                        <select
                            v-model="form.model"
                            class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                        >
                            <template v-if="form.api_provider === 'openai'">
                                <option value="gpt-4">GPT-4</option>
                                <option value="gpt-4-turbo">GPT-4 Turbo</option>
                                <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                            </template>
                            <template v-else>
                                <option value="claude-3-opus">Claude 3 Opus</option>
                                <option value="claude-3-sonnet">Claude 3 Sonnet</option>
                                <option value="claude-haiku-4-5-20251001">Claude 3 Haiku</option>
                            </template>
                        </select>
                    </div>

                    <!-- Connection Test -->
                    <div class="gb-pt-2">
                        <button
                            type="button"
                            @click="testConnection"
                            :disabled="!form.api_key || settingsStore.connectionTesting"
                            class="gb-inline-flex gb-items-center gb-gap-2 gb-px-4 gb-py-2 gb-bg-gray-100 gb-text-gray-700 gb-rounded-md hover:gb-bg-gray-200 disabled:gb-opacity-50 disabled:gb-cursor-not-allowed"
                        >
                            <svg v-if="settingsStore.connectionTesting" class="gb-animate-spin gb-w-4 gb-h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>{{ settingsStore.connectionTesting ? 'Testing...' : 'Test Connection' }}</span>
                        </button>

                        <div
                            v-if="settingsStore.connectionStatus"
                            :class="[
                                'gb-mt-3 gb-p-3 gb-rounded-md gb-text-sm',
                                settingsStore.connectionStatus.success
                                    ? 'gb-bg-green-50 gb-text-green-700'
                                    : 'gb-bg-red-50 gb-text-red-700'
                            ]"
                        >
                            {{ settingsStore.connectionStatus.message }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generation Settings -->
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Generation Settings</h2>

                <div class="gb-space-y-4">
                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            Max Tokens
                        </label>
                        <input
                            type="number"
                            v-model.number="form.max_tokens"
                            min="100"
                            max="16000"
                            class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                        />
                        <p class="gb-text-xs gb-text-gray-500 gb-mt-1">Maximum tokens for AI response (100-16000)</p>
                    </div>

                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            Temperature: {{ form.temperature }}
                        </label>
                        <input
                            type="range"
                            v-model.number="form.temperature"
                            min="0"
                            max="1"
                            step="0.1"
                            class="gb-w-full"
                        />
                        <p class="gb-text-xs gb-text-gray-500 gb-mt-1">Higher values = more creative, lower = more focused</p>
                    </div>

                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            Rate Limit (requests/day)
                        </label>
                        <input
                            type="number"
                            v-model.number="form.rate_limit"
                            min="1"
                            max="1000"
                            class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                        />
                    </div>
                </div>
            </div>

            <!-- Cache Settings -->
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Cache Settings</h2>

                <div class="gb-space-y-4">
                    <div class="gb-flex gb-items-center gb-justify-between">
                        <div>
                            <label class="gb-text-sm gb-font-medium gb-text-gray-700">Enable Cache</label>
                            <p class="gb-text-xs gb-text-gray-500">Cache AI responses to reduce API calls</p>
                        </div>
                        <button
                            type="button"
                            @click="form.cache_enabled = !form.cache_enabled"
                            :class="[
                                'gb-relative gb-inline-flex gb-h-6 gb-w-11 gb-flex-shrink-0 gb-cursor-pointer gb-rounded-full gb-border-2 gb-border-transparent gb-transition-colors gb-duration-200 gb-ease-in-out',
                                form.cache_enabled ? 'gb-bg-blue-600' : 'gb-bg-gray-200'
                            ]"
                        >
                            <span
                                :class="[
                                    'gb-pointer-events-none gb-inline-block gb-h-5 gb-w-5 gb-transform gb-rounded-full gb-bg-white gb-shadow gb-ring-0 gb-transition gb-duration-200 gb-ease-in-out',
                                    form.cache_enabled ? 'gb-translate-x-5' : 'gb-translate-x-0'
                                ]"
                            ></span>
                        </button>
                    </div>

                    <div v-if="form.cache_enabled">
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            Cache Duration (seconds)
                        </label>
                        <input
                            type="number"
                            v-model.number="form.cache_duration"
                            min="60"
                            max="86400"
                            class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                        />
                    </div>

                    <div class="gb-pt-2">
                        <button
                            type="button"
                            @click="clearCache"
                            :disabled="settingsStore.cacheClearing"
                            class="gb-inline-flex gb-items-center gb-gap-2 gb-px-4 gb-py-2 gb-bg-gray-100 gb-text-gray-700 gb-rounded-md hover:gb-bg-gray-200 disabled:gb-opacity-50"
                        >
                            <svg v-if="settingsStore.cacheClearing" class="gb-animate-spin gb-w-4 gb-h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>{{ settingsStore.cacheClearing ? 'Clearing...' : 'Clear Cache' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Default Styles -->
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Default Styles</h2>

                <div class="gb-space-y-4">
                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            Primary Color
                        </label>
                        <div class="gb-flex gb-items-center gb-gap-3">
                            <input
                                type="color"
                                v-model="form.primary_color"
                                class="gb-w-12 gb-h-10 gb-rounded gb-border gb-border-gray-300 gb-cursor-pointer"
                            />
                            <input
                                type="text"
                                v-model="form.primary_color"
                                class="gb-flex-1 gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                                placeholder="#0073aa"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="gb-block gb-text-sm gb-font-medium gb-text-gray-700 gb-mb-1">
                            Default Text Alignment
                        </label>
                        <select
                            v-model="form.default_alignment"
                            class="gb-w-full gb-border gb-border-gray-300 gb-rounded-md gb-px-3 gb-py-2 focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                        >
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="gb-flex gb-items-center gb-gap-4">
                <button
                    type="submit"
                    :disabled="settingsStore.saving"
                    class="gb-inline-flex gb-items-center gb-gap-2 gb-px-6 gb-py-2 gb-bg-blue-600 gb-text-white gb-rounded-md hover:gb-bg-blue-700 disabled:gb-opacity-50 disabled:gb-cursor-not-allowed"
                >
                    <svg v-if="settingsStore.saving" class="gb-animate-spin gb-w-4 gb-h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>{{ settingsStore.saving ? 'Saving...' : 'Save Settings' }}</span>
                </button>

                <span v-if="saveMessage" :class="saveMessage.type === 'success' ? 'gb-text-green-600' : 'gb-text-red-600'" class="gb-text-sm">
                    {{ saveMessage.text }}
                </span>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useSettingsStore } from '../stores/settings';

const settingsStore = useSettingsStore();
const showApiKey = ref(false);
const saveMessage = ref(null);

const form = reactive({
    api_provider: 'openai',
    api_key: '',
    model: 'gpt-4',
    max_tokens: 2000,
    temperature: 0.7,
    rate_limit: 100,
    cache_enabled: true,
    cache_duration: 3600,
    primary_color: '#0073aa',
    default_alignment: 'center',
});

onMounted(async () => {
    await settingsStore.loadSettings();
    Object.assign(form, settingsStore.settings);
});

watch(() => settingsStore.settings, (newSettings) => {
    Object.assign(form, newSettings);
}, { deep: true });

async function saveSettings() {
    saveMessage.value = null;
    try {
        await settingsStore.updateSettings({ ...form });
        saveMessage.value = { type: 'success', text: 'Settings saved successfully!' };
        setTimeout(() => {
            saveMessage.value = null;
        }, 3000);
    } catch (error) {
        saveMessage.value = { type: 'error', text: error.message || 'Failed to save settings' };
    }
}

async function testConnection() {
    try {
        await settingsStore.testApiConnection();
    } catch (error) {
        // Error is handled by the store
    }
}

async function clearCache() {
    try {
        await settingsStore.clearCache();
        saveMessage.value = { type: 'success', text: 'Cache cleared successfully!' };
        setTimeout(() => {
            saveMessage.value = null;
        }, 3000);
    } catch (error) {
        saveMessage.value = { type: 'error', text: error.message || 'Failed to clear cache' };
    }
}
</script>
