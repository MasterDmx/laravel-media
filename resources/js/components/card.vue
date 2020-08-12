<template >
    <div class="mm-card" @click="select" @dblclick="use">
        <div class="mm-card__wrapp" :class="{ 'active': state.file !== null && (state.file.id == file.id)}">
            <div class="mm-card__img-box">
                <img :src="file.url" alt="" class="mm-card__img">
            </div>
            <div class="mm-card__rename" v-if="rename">
                <input ref="rename" type="text" @blur="renameStop" @keyup.enter="renameStop" v-model="file.name">
            </div>
            <div class="mm-card__name" v-else @dblclick="renameStart">{{ file.name }}</div>
            <div class="mm-card__filename">2020-06-23</div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        file: Object,
        state: Object,
    },
    data: function () {
        return {
            rename: false,
        }
    },
    methods: {
        renameStart: function() {
            this.rename = true;
            this.$nextTick(() => {
                this.$refs['rename'].select();
            });
        },
        renameStop: function() {
            this.rename = false;
        },
        select: function () {
            if (this.state.file === null || this.state.file === undefined || this.file.id !== this.state.file.id) {
                this.$emit('select-file', this.file);
            }
        },
        use: function () {
            this.$emit('use-file', this.file);
        },
    }
}
</script>
