module.exports = {
  globDirectory: "app/",
  globPatterns: ["**/*.{,png,otf,svg,eps,pdn,jpg,mp4,gif,webp,webm,xml}"],
  swDest: "app/sw.js",
  ignoreURLParametersMatching: [/^utm_/, /^fbclid$/],
  globIgnores: ["profile/u/**/*"],
  runtimeCaching: [
    {
      urlPattern: /\.(?:png|jpg|jpeg|svg|gif|webp)$/,
      handler: "CacheFirst",
      options: {
        cacheName: "images-cache",
        expiration: {
          maxEntries: 50,
          maxAgeSeconds: 30 * 24 * 60 * 60,
        },
      },
    },
    {
      urlPattern: /\.(?:mp4|webm)$/,
      handler: "CacheFirst",
      options: {
        cacheName: "videos-cache",
        expiration: {
          maxEntries: 10,
          maxAgeSeconds: 30 * 24 * 60 * 60,
        },
      },
    },
  ],
};
