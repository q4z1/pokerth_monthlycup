<template>
    <div>
        <div class="panel" v-if="nextCup">
            <h2 class="section-title">Next cup: {{ nextCup.month_name }} {{ year }}</h2>
            <p>Scheduled for <strong>{{ nextCup.date_label }}</strong> &mdash; that is in
                <strong>{{ countdown }}</strong>.</p>
            <p>{{ signupCount }} player(s) accepted so far.</p>
            <el-space wrap>
                <el-button type="primary" @click="go(registrationUrl)">
                    <el-icon><Edit /></el-icon>&nbsp;Register
                </el-button>
                <el-button @click="go(signupsUrl)">
                    <el-icon><UserFilled /></el-icon>&nbsp;Signup list
                </el-button>
            </el-space>
        </div>

        <div class="panel" v-else>
            <h2 class="section-title">Season {{ year }}</h2>
            <p>No upcoming cup is scheduled at the moment.</p>
            <el-button @click="go(resultsUrl)">
                <el-icon><Notebook /></el-icon>&nbsp;Season results
            </el-button>
        </div>

        <div class="panel" v-if="latestCup && latestCup.podium.length">
            <h2 class="section-title">{{ latestCup.month_name }} Cup &mdash; gold table</h2>
            <el-table :data="latestCup.podium" style="width:100%">
                <el-table-column prop="position" label="Place" width="90" />
                <el-table-column prop="playername" label="Player" />
            </el-table>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        year: { type: Number, required: true },
        nextCup: { type: Object, default: null },
        signupCount: { type: Number, default: 0 },
        latestCup: { type: Object, default: null },
        registrationUrl: { type: String, required: true },
        signupsUrl: { type: String, required: true },
        resultsUrl: { type: String, required: true },
    },
    data() {
        return { now: Date.now(), timer: null };
    },
    computed: {
        countdown() {
            if (!this.nextCup) return '';
            const diff = new Date(this.nextCup.date).getTime() - this.now;
            if (diff <= 0) return 'a moment';
            const days = Math.floor(diff / 86400000);
            const hours = Math.floor((diff % 86400000) / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const parts = [];
            if (days) parts.push(`${days} day${days === 1 ? '' : 's'}`);
            if (hours) parts.push(`${hours} hour${hours === 1 ? '' : 's'}`);
            parts.push(`${minutes} minute${minutes === 1 ? '' : 's'}`);
            return parts.join(', ');
        },
    },
    mounted() {
        this.timer = setInterval(() => { this.now = Date.now(); }, 30000);
    },
    beforeUnmount() {
        clearInterval(this.timer);
    },
    methods: {
        go(url) { window.location.href = url; },
    },
};
</script>
