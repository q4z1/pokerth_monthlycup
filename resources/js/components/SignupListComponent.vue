<template>
    <div>
        <h1 class="page-title">Signups for the {{ cup.month_name }} Cup</h1>
        <p class="page-subtitle" v-if="cup.date_label">
            Scheduled for <strong>{{ cup.date_label }}</strong>
        </p>

        <el-alert v-if="pending" type="info" :closable="false" show-icon style="margin-bottom:1rem"
                  :title="pendingTitle">
            Registrations appear in this list once a member of the orga team has accepted them.
        </el-alert>

        <div class="panel">
            <el-table v-if="players.length" :data="players" style="width:100%" stripe>
                <el-table-column prop="no" label="No." width="80" />
                <el-table-column label="Player">
                    <template #default="scope">
                        <a class="player-link" target="_blank" rel="noopener"
                           :href="playerUrl + encodeURIComponent(scope.row.playername)">
                            {{ scope.row.playername }}
                        </a>
                    </template>
                </el-table-column>
            </el-table>

            <el-empty v-else :description="emptyText">
                <el-button v-if="cup.open" type="primary" @click="go(registrationUrl)">
                    Register now
                </el-button>
            </el-empty>
        </div>

        <div class="panel" v-if="substitutes.length">
            <h2 class="section-title">Substitutes</h2>
            <p>{{ substitutes.join(', ') }}</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        cup: { type: Object, required: true },
        players: { type: Array, default: () => [] },
        substitutes: { type: Array, default: () => [] },
        pending: { type: Number, default: 0 },
        registrationUrl: { type: String, default: '' },
        playerUrl: { type: String, required: true },
    },
    computed: {
        pendingTitle() {
            return this.pending === 1
                ? '1 registration is waiting to be accepted'
                : `${this.pending} registrations are waiting to be accepted`;
        },
        emptyText() {
            return this.pending
                ? 'No registration has been accepted yet.'
                : 'No signups found.';
        },
    },
    methods: {
        go(url) { window.location.href = url; },
    },
};
</script>
