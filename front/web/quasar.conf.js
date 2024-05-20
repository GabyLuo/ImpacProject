// Configuration for your app
const myConfig = require('./config.env')

module.exports = function (ctx) {
  return {
    // app plugins (/src/plugins)
    plugins: [
      'axios',
      'i18n',
      'moment',
      'vuelidate',
      // 'messages',
      'helpers'
      // 'nvd3'
    ],
    css: [
      'app.styl'
    ],
    extras: [
      ctx.theme.mat ? 'roboto-font' : null,
      'material-icons',
      // 'ionicons',
      // 'mdi',
      'fontawesome'
    ],
    supportIE: true,
    vendor: {
      add: [],
      remove: []
    },
    build: {
      distDir: process.env.PROD ? 'prod/' : 'dist/',
      scopeHoisting: true,
      vueRouterMode: 'history',
      // gzip: true,
      // analyze: true,
      // extractCSS: false,
      // useNotifier: true,
      env: ctx.dev
        ? { // so on dev we'll have
          API: JSON.stringify(myConfig.BACKEND_LOCAL_URL)
        }
        : process.env.PROD
          ? { // and on build (production):
            API: JSON.stringify(myConfig.BACKEND_PROD_URL)
          } : { // and on build (development):
            API: JSON.stringify(myConfig.BACKEND_BETA_URL)
          },
      extendWebpack (cfg) {
        cfg.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /(node_modules|quasar)/
        })
      }
    },
    devServer: {
      // https: true,
      // port: 8080,
      open: true // opens browser window automatically
    },
    // framework: 'all' --- includes everything; for dev only!
    framework: {
      i18n: 'es',
      components: [
        'QSlideTransition',
        'QInnerLoading',
        'QSpinnerDots',
        'QChipsInput',
        'QLayout',
        'QLayoutHeader',
        'QLayoutDrawer',
        'QPageContainer',
        'QUploader',
        'QPage',
        'QProgress',
        'QToolbar',
        'QToolbarTitle',
        'QBtnGroup',
        'QBtnDropdown',
        'QBtn',
        'QIcon',
        'QList',
        'QListHeader',
        'QItem',
        'QItemMain',
        'QItemTile',
        'QItemSide',
        'QCard',
        'QCardTitle',
        'QCardActions',
        'QCardMain',
        'QCardSeparator',
        'QModal',
        'QModalLayout',
        'QField',
        'QInput',
        'QDatetime',
        'QSelect',
        'QCheckbox',
        'QRadio',
        'QOptionGroup',
        'QTable',
        'QTh',
        'QTr',
        'QTd',
        'QToggle',
        'QTableColumns',
        'QSearch',
        'QTooltip',
        'QScrollArea',
        'QChip',
        'QCollapsible',
        'QPopover',
        'QTabs',
        'QTab',
        'QTabPane',
        'QRouteTab',
        'QTimeline',
        'QTimelineEntry',
        'QAutocomplete',
        'QTree',
        'QPagination',
        'QKnob',
        'QSlider',
        'QBreadcrumbs',
        'QBreadcrumbsEl',
        'QBtnToggle',
        'QCarousel',
        'QCarouselSlide',
        'QCarouselControl',
        'QDatetimePicker'
      ],
      directives: [
        'Ripple',
        'CloseOverlay'
      ],
      // Quasar plugins
      plugins: [
        'Notify',
        'Dialog',
        'Loading'
      ]
    },
    // animations: 'all' --- includes all animations
    animations: [
      'bounceInLeft',
      'bounceOut'
    ],
    pwa: {
      cacheExt: 'js,html,css,ttf,eot,otf,woff,woff2,json,svg,gif,jpg,jpeg,png,wav,ogg,webm,flac,aac,mp4,mp3',
      manifest: {
        // name: 'Quasar App',
        // short_name: 'Quasar-PWA',
        // description: 'Best PWA App in town!',
        display: 'standalone',
        orientation: 'portrait',
        background_color: '#ffffff',
        theme_color: '#027be3',
        icons: [
          {
            'src': 'statics/icons/icon-128x128.png',
            'sizes': '128x128',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-192x192.png',
            'sizes': '192x192',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-256x256.png',
            'sizes': '256x256',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-384x384.png',
            'sizes': '384x384',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-512x512.png',
            'sizes': '512x512',
            'type': 'image/png'
          }
        ]
      }
    },
    cordova: {
      // id: 'org.cordova.quasar.app'
    },
    electron: {
      extendWebpack (cfg) {
        // do something with cfg
      },
      packager: {
        // OS X / Mac App Store
        // appBundleId: '',
        // appCategoryType: '',
        // osxSign: '',
        // protocol: 'myapp://path',

        // Window only
        // win32metadata: { ... }
      }
    },

    // leave this here for Quasar CLI
    starterKit: '1.0.2'
  }
}
