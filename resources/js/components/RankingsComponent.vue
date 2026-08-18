<template>
    <div>
        <h1 class="page-title">Rankings {{ year }}</h1>
        <p class="page-subtitle">Cup ranking points (CRP) of the season</p>

        <div class="panel">
            <h2 class="section-title">General ranking</h2>
            <div class="table-scroll">
                <el-table :data="general" style="width:100%" stripe max-height="640">
                    <el-table-column prop="rank" label="Rank" width="80" fixed />
                    <el-table-column label="Player" width="200" fixed>
                        <template #default="scope">
                            <a class="player-link" target="_blank" rel="noopener"
                               :href="playerUrl + encodeURIComponent(scope.row.playername)">
                                {{ scope.row.playername }}
                            </a>
                        </template>
                    </el-table-column>
                    <el-table-column prop="points" label="Total CRPs" width="120" sortable />
                    <el-table-column v-for="column in monthColumns" :key="column.month"
                                     :label="column.name" width="120">
                        <template #default="scope">
                            {{ scope.row.months[column.month] ?? '' }}
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>

        <div class="panel" v-for="month in months" :key="month.month">
            <h2 class="section-title">{{ month.month_name }} Cup</h2>
            <el-table :data="month.rows" style="width:100%" stripe max-height="520">
                <el-table-column prop="rank" label="Rank" width="80" />
                <el-table-column label="Player">
                    <template #default="scope">
                        <a class="player-link" target="_blank" rel="noopener"
                           :href="playerUrl + encodeURIComponent(scope.row.playername)">
                            {{ scope.row.playername }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="points" :label="month.month_name + ' CRPs'" width="150" sortable />
            </el-table>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        year: { type: Number, required: true },
        general: { type: Array, default: () => [] },
        months: { type: Array, default: () => [] },
        monthColumns: { type: Array, default: () => [] },
        playerUrl: { type: String, required: true },
    },
};
</script>
