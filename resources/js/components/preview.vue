<template>
    <div class="mm-preview">
        <div class="mm-preview__wrapp">
            <div class="mm-preview__preview">
                <img :src="file.url" alt="">
            </div>
            <div class="mm-preview__info">

                <div class="mm-info-list">
                    <div class="mm-info">
                        <div class="mm-info__title" @click="test">Название</div>
                        <div class="mm-info__content">{{ file.name }}</div>
                    </div>

                    <div class="mm-info">
                        <div class="mm-info__title">Путь</div>
                        <div class="mm-info__content">{{ file.path }}</div>
                    </div>

                    <div class="mm-info">
                        <div class="mm-info__title">Загружен</div>
                        <div class="mm-info__content">{{ createdAt }}</div>
                    </div>

                    <div class="mm-info">
                        <div class="mm-info__title">Обновлен</div>
                        <div class="mm-info__content">{{ updatedAt }}</div>
                    </div>

                    <div class="mm-info" v-if="config.userMode">
                        <div class="mm-info__title">Пользователь</div>
                        <div class="mm-info__content">Сергей</div>
                    </div>
                </div>

            </div>
            <div class="mm-preview__use" style="    margin-top: 60px;">
                <button class="mm-button" @click="useFile">Использовать</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        file: Object,
        config: Object
    },
    computed: {
        createdAt: function () {
            return new Date(this.file.created_at).toLocaleString();
        },
        updatedAt: function () {
            return new Date(this.file.created_at).toLocaleString();
        },
    },
    methods: {
        useFile: function () {
            this.$emit('use-file', this.file);
        },
        test: function () {
            console.log('--------------------------------');
            console.log(this.file);
            console.log('--------------------------------');
        },
    }
}
</script>
