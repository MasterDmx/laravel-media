'use strict'

import VModal from 'vue-js-modal/dist/index.nocss.js';
import VueProgressBar from 'vue-progressbar';
import VueMediaManager from './manager.vue';

function install (Vue, options = {}) {
    Vue.use(VueProgressBar, {
        color: '#00BC7E',
        failedColor: '#874b4b',
        thickness: '2px',
        transition: {
          speed: '0.2s',
          opacity: '0.6s',
          termination: 300
        },
        autoRevert: true,
        location: 'top',
        inverse: false
    })

    Vue.use(VModal)

    const data = {
        config: {
            api: {
                init:   '/vendor/media/manager/init',
                files:  '/vendor/media/manager/files',
                upload: '/vendor/media/manager/files/upload',
                remove: '/vendor/media/manager/files/',
            },
            storage: {
                url: ''
            },
            userMode: false,
            extensions: [],
            initialized: false,
        },
        files: [],
        filesLoaded: false,
        state: {
            file: null,
        },
        searchRequest: null,
        loading: false,
        opened: false,
        useCallback: null,
        removeCallback: null
    }

    const privateMethods = {
        init: function (callback = null) {
            this.initConfig(() => {
                this.loadFiles(() => {
                    if (callback) {
                        callback();
                    }
                });
            });
        },

        initConfig: function (callback = null) {
            if (!MediaManager.data.config.initialized) {
                axios.get(MediaManager.data.config.api.init).then(response => {
                    MediaManager.data.config.extensions = response.data.allow_extensions;
                    MediaManager.data.config.storage.url = response.data.storage_url;
                    MediaManager.data.config.userMode = response.data.user_mode;
                    MediaManager.data.config.initialized = true;
                }).catch(error => {
                    if (error.response) {
                        console.log(error.response.data);
                        console.log(error.response.status);
                        console.log(error.response.headers);
                    }

                    alert('Media manager: cofig load error');
                }).finally(() => {
                    if (callback) {
                        callback();
                    }
                });
            } else if (callback) {
                callback();
            }
        },

        loadFiles: function (callback = null) {
            if (!MediaManager.data.filesLoaded) {
                axios.get(MediaManager.data.config.api.files).then(response => {
                    MediaManager.data.files = response.data;
                    MediaManager.data.filesLoaded = true;
                }).catch(error => {
                    if (error.response) {
                        console.log(error.response.data);
                        console.log(error.response.status);
                        console.log(error.response.headers);
                    }

                    alert('Media manager: files load error');
                }).finally(() => {
                    if (callback) {
                        callback();
                    }
                });
            } else if (callback) {
                callback();
            }
        },

        /**
         * Найти файл по пути
         * @param {string} path
         */
        findFileByPaths: function (path) {
            for (let index = 0; index < MediaManager.data.files.length; index++) {
                const file = MediaManager.data.files[index];

                if (file.path === path) {
                    return file;
                }
            }

            return null;
        },
    };

    const MediaManager = {
        data: data,
        private: privateMethods,

        // Открыть менеджер
        open: function (options = {}) {
            this.data.opened = true;
            this.data.useCallback = options.useCallback || null;
            this.data.removeCallback = options.removeCallback || null;

            this.private.init(() => {
                if (options.activateFileByPath || null) {
                    let file = this.private.findFileByPaths(options.activateFileByPath);

                    if (file) {
                        this.data.state.file = file;
                    }
                }
            });
        },

        /**
         * Получить публичный URL файла по пути
         * @param {string} path
         */
        getFileUrlByPath: function (path) {
            return this.data.config.storage.url + path;
        },
    }

    Vue.prototype.$MediaManager = MediaManager;
    Vue.component('media-manager', VueMediaManager);
}

export default {
    install
}
