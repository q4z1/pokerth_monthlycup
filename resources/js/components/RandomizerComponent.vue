<template>
    <div>
        <h1 class="page-title">Random seeding &mdash; {{ monthName }} Cup {{ year }}</h1>
        <p class="page-subtitle">Generated {{ generatedAt }}</p>

        <div class="panel">
            <div style="display:flex; gap:0.75rem; margin-bottom:1rem; flex-wrap:wrap">
                <el-button type="primary" @click="reload">
                    <el-icon><Refresh /></el-icon>&nbsp;Shuffle again
                </el-button>
                <el-button @click="copy">
                    <el-icon><CopyDocument /></el-icon>&nbsp;Copy list
                </el-button>
            </div>

            <pre class="seed-list" ref="list">{{ players.join('\n') }}</pre>
            <el-empty v-if="!players.length" description="No accepted signups found." />
        </div>

        <div class="panel" v-if="substitutes.length">
            <h2 class="section-title">Substitutes</h2>
            <p>{{ substitutes.join(', ') }}</p>
        </div>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus/es/components/message/index';

export default {
    props: {
        year: { type: Number, required: true },
        month: { type: Number, required: true },
        monthName: { type: String, required: true },
        players: { type: Array, default: () => [] },
        substitutes: { type: Array, default: () => [] },
        generatedAt: { type: String, default: '' },
    },
    methods: {
        reload() { window.location.reload(); },
        copy() {
            navigator.clipboard.writeText(this.players.join('\n'))
                .then(() => ElMessage.success('Seeding order copied.'))
                .catch(() => ElMessage.error('Copying failed.'));
        },
    },
};
</script>
