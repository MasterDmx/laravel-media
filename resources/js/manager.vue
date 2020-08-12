<template>
    <div>
        <div class="mm">
            <modal name="im" width="95%" height="auto" @before-close="beforeCloseModal">
                <div class="mm-ui">

                    <vue-progress-bar></vue-progress-bar>

                    <div class="mm-ui__wrapp">
                        <div class="mm-workspace-loader" v-if="!isLoaded">
                            <div class="mm-loader"></div>
                        </div>
                        <div class="mm-workspace" v-else>
                            <div class="mm-toolbar">
                                <div class="mm-toolbar__wrapp">
                                    <div class="mm-toolbar__title" style="font-weight: 600;color: #00BC7E;">Файловый менеджер</div>

                                    <div class="mm-toolbar__searchbox">
                                        <div class="mm-search-bar">
                                            <input type="text" name="query" placeholder="Поиск" v-model="searchRequest" class="mm-search-bar__field">
                                            <div class="mm-search-bar__icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="19px" height="19px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle data-v-01d307b9="" cx="11" cy="11" r="8"></circle><line data-v-01d307b9="" x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mm-toolbar__tools">
                                        <div class="mm-toolbar__group">
                                            <input id="mm-file-selector" ref="fileSelector" type="file" @change="uploadFile" hidden>

                                            <button class="mm-icon-button mm-icon-button--svg mm-toolbar__tool" @click="chooseFile()" >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload-cloud im-icon vfm-svg-icon"><polyline data-v-3a42fd9f="" points="16 16 12 12 8 16"></polyline><line data-v-3a42fd9f="" x1="12" y1="12" x2="12" y2="21"></line><path data-v-3a42fd9f="" d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path><polyline data-v-3a42fd9f="" points="16 16 12 12 8 16"></polyline></svg>
                                            </button>
                                        </div>

                                        <div class="mm-toolbar__group">
                                            <button class="mm-icon-button mm-icon-button--svg mm-toolbar__tool" @click="remove()" :disabled="isNotSelected">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="19px" height="19px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 im-icon vfm-svg-icon"><polyline data-v-354943e0="" points="3 6 5 6 21 6"></polyline><path data-v-354943e0="" d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line data-v-354943e0="" x1="10" y1="11" x2="10" y2="17"></line><line data-v-354943e0="" x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                        </div>

                                        <div class="mm-toolbar__group">
                                            <button class="mm-icon-button mm-icon-button--svg mm-toolbar__tool" @click="closeModal()">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px" viewBox="0 0 24 24" class="vfm-svg-icon" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M0 0h24v24H0z" fill="none" style="stroke: none;"/>
                                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="mm-content">
                                <template v-if="loading || isNotFiles">
                                    <div class="mm-content__manager">
                                        <div class="mm-content__informer">
                                            <div class="mm-text-informer" v-if="isNotFiles">Файлов нет</div>
                                            <div class="mm-loader" v-else>Loading...</div>
                                        </div>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="mm-content__manager" @click.self="resetSelectedFile()">
                                        <div class="mm-files" @click.self="resetSelectedFile()">
                                            <card v-for="file in activeFiles" :key="file.id" :file="file" :state="state" @use-file="useFileEvent" @select-file="setFile"></card>
                                        </div>
                                    </div>
                                    <div class="mm-content__preview">
                                        <div class="mm-empty-preview" v-if="isNotSelected">
                                            <div class="mm-empty-preview__wrapp">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="36px" height="36px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon feather feather-eye-off"><path data-v-f1b82072="" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line data-v-f1b82072="" x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            </div>
                                        </div>
                                        <preview v-else :file="state.file" :config="config" @use-file="useFileEvent"></preview>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </div>

                </div>
            </modal>
        </div>
    </div>
</template>

<script src="./manager.js"></script>
