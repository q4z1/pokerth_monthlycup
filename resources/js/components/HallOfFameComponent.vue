<template>
    <div>
        <h1 class="page-title">Hall of Fame {{ year }}</h1>
        <p class="page-subtitle">Everybody who earned an award this season</p>

        <div class="panel">
            <el-table :data="players" style="width:100%" stripe>
                <el-table-column prop="rank" label="Pos." width="80" />
                <el-table-column label="Avatar" width="120">
                    <template #default="scope">
                        <el-avatar v-if="scope.row.avatar_url" :size="84" :src="scope.row.avatar_url" />
                        <el-avatar v-else :size="84">{{ scope.row.playername.charAt(0).toUpperCase() }}</el-avatar>
                    </template>
                </el-table-column>
                <el-table-column label="Player" width="260">
                    <template #default="scope">
                        <a class="player-link player-name" target="_blank" rel="noopener"
                           :href="playerUrl + encodeURIComponent(scope.row.playername)">
                            {{ scope.row.playername }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="points" label="Points" width="110" sortable />
                <el-table-column label="Awards">
                    <template #default="scope">
                        <div class="award-strip">
                            <el-tooltip v-for="award in scope.row.awards" :key="award.url"
                                        :content="award.label" placement="top">
                                <img :src="award.url" :alt="award.label" loading="lazy">
                            </el-tooltip>
                        </div>
                    </template>
                </el-table-column>
            </el-table>

            <el-empty v-if="!players.length" description="No awards handed out yet." />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        year: { type: Number, required: true },
        players: { type: Array, default: () => [] },
        playerUrl: { type: String, required: true },
    },
};
</script>
