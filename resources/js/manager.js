import card from './components/card.vue'
import preview from './components/preview.vue'

export default {
    components: {
        card,
        preview
    },
    data: function () {
        return this.$MediaManager.data;
    },
    computed: {
        isLoaded: function () {
            return this.config.initialized && this.filesLoaded;
        },
        searchedFiles: function () {
            return this.searchRequest == null ? this.files : this.files.filter(el => el.name.toLowerCase().indexOf(this.searchRequest.toLowerCase()) != -1)
        },

        activeFiles: function () {
            return this.searchedFiles;
        },

        isNotFiles: function () {
            return this.activeFiles === null || this.activeFiles === undefined || this.activeFiles.length == 0;
        },

        isNotSelected: function () {
            return this.state.file === null || this.state.file === undefined;
        },
    },
    watch: {
        opened: function () {
            if (this.opened) {
                this.openModal();
            }
        }
    },
    methods: {
        сlose: function () {
            this.closeModal();
        },

        use: function () {
            if (this.useCallback) {
                this.useCallback(this.state.file);
            }

            this.сlose();
        },

        /**
         * Использование файла
         * @event select-file
         */
        useFileEvent: function (file) {
            this.use();
        },

        setFile: function (file) {
            this.state.file = file;
        },

        /**
         * Выбор файла для загрузки
         */
        chooseFile: function() {
            document.getElementById("mm-file-selector").click();
        },

        /**
         * Загрузка файла на сервер
         */
        uploadFile: function() {
            let file = this.$refs.fileSelector.files[0];

            if (file) {
                this.$Progress.start();
                let extension = file.name.split(".").pop()

                if (!this.checkExtension(extension)) {
                    alert('Расширение "' + extension + '" не поддерживается');
                    return;
                }

                let formData = new FormData();
                formData.append('file', file);

                axios.post(this.config.api.upload, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    let file = response.data;
                    this.files.unshift(file);
                    this.setFile(file);
                    this.$Progress.finish();
                }).catch(error => {
                    console.log('Ошибка загрузки: ');

                    if (error.response) {
                        console.log(error.response.data);
                        console.log(error.response.status);
                        console.log(error.response.headers);
                    }

                    console.log('-------------------------');
                });
            } else {
                this.$Progress.finish();
            }
        },

        /**
         * Проверить доступность на заливку
         */
        checkExtension: function (extension) {
            if (this.config.extensions === undefined || this.config.extensions == 0) {
                return true;
            }

            return (this.config.extensions.indexOf(extension.toLowerCase()) + 1) > 0;
        },

        remove: function() {
            if (this.state.file && confirm('Подтвердите удаление')) {
                var file = this.state.file;
                var id = file.id;

                axios.delete(this.config.api.remove + id).then(response => {
                    this.files.splice(this.getIndexFileById(id), 1);

                    this.resetSelectedFile();

                    if (this.removeCallback) {
                        this.removeCallback(file);
                    }
                });
            }
        },

        getIndexFileById: function (id) {
            for (let index = 0; index < this.files.length; index++) {
                const file = this.files[index];

                if (file.id === id) {
                    return index
                }
            }
        },

        resetSelectedFile: function () {
            this.state.file = null;
        },

        closeModal: function () {
            this.$modal.hide('im');
        },
        openModal: function () {
            this.$modal.show('im');
        },
        beforeCloseModal: function () {
            this.opened = false;
        }
    }
}
