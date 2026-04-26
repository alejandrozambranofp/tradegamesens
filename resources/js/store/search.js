import { defineStore } from 'pinia'

export const useSearchStore = defineStore('search', {
    state: () => ({
        query: '',
        gameId: null,
        categoryId: null,
    }),
    actions: {
        setQuery(q) {
            this.query = q
        },
        setGame(id) {
            this.gameId = id
        },
        setCategory(id) {
            this.categoryId = id
        },
        clearFilters() {
            this.query = ''
            this.gameId = null
            this.categoryId = null
        }
    },
    persist: true
})
