<template>
    <div>
        <div class="panel">
            <el-row :gutter="16">
                <el-col :xs="12" :sm="8" :md="4" v-for="stat in tiles" :key="stat.label">
                    <el-statistic :title="stat.label" :value="stat.value" />
                </el-col>
            </el-row>
        </div>

        <div class="panel">
            <h2 class="section-title">Quick actions</h2>
            <el-space wrap>
                <el-button v-for="link in links" :key="link.url" @click="go(link.url)">
                    <el-icon><component :is="link.icon" /></el-icon>&nbsp;{{ link.label }}
                </el-button>
            </el-space>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        stats: { type: Object, required: true },
        links: { type: Array, default: () => [] },
    },
    computed: {
        tiles() {
            return [
                { label: 'Signups', value: this.stats.signups },
                { label: 'Accepted', value: this.stats.signups_accepted },
                { label: 'Pending', value: this.stats.signups - this.stats.signups_accepted },
                { label: 'Players', value: this.stats.players },
                { label: 'Awards', value: this.stats.awards },
                { label: 'Tables uploaded', value: this.stats.uploaded_tables },
            ];
        },
    },
    methods: {
        go(url) { window.location.href = url; },
    },
};
</script>
