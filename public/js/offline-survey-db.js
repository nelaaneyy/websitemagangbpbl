/**
 * WebGIS SIPELITA - Client-Side IndexedDB Offline Handler
 * Untuk Petugas Lapangan & Survei Area Blank Spot (Tanpa Sinyal)
 */

class OfflineSurveyDB {
    constructor() {
        this.dbName = 'SipelitaOfflineDB';
        this.dbVersion = 1;
        this.db = null;
        this.initDB();
        this.registerOnlineListener();
    }

    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = (event) => {
                console.error("IndexedDB Error:", event.target.errorCode);
                reject(event.target.errorCode);
            };

            request.onsuccess = (event) => {
                this.db = event.target.result;
                console.log("Sipelita Offline Storage Ready.");
                this.updateOfflineBadgeCount();
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains('survey_drafts')) {
                    const store = db.createObjectStore('survey_drafts', { keyPath: 'id', autoIncrement: true });
                    store.createIndex('nik', 'nik', { unique: false });
                    store.createIndex('created_at', 'created_at', { unique: false });
                }
            };
        });
    }

    async saveSurveyDraft(surveyData) {
        if (!this.db) await this.initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['survey_drafts'], 'readwrite');
            const store = transaction.objectStore('survey_drafts');
            
            surveyData.created_at = new Date().toISOString();
            surveyData.status = 'offline_draft';

            const request = store.add(surveyData);
            
            request.onsuccess = () => {
                console.log("Draft survei berhasil disimpan secara lokal (Offline Mode).");
                this.updateOfflineBadgeCount();
                resolve(request.result);
            };

            request.onerror = (e) => reject(e.target.error);
        });
    }

    async getAllDrafts() {
        if (!this.db) await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['survey_drafts'], 'readonly');
            const store = transaction.objectStore('survey_drafts');
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = (e) => reject(e.target.error);
        });
    }

    async deleteDraft(id) {
        if (!this.db) await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['survey_drafts'], 'readwrite');
            const store = transaction.objectStore('survey_drafts');
            const request = store.delete(id);

            request.onsuccess = () => {
                this.updateOfflineBadgeCount();
                resolve(true);
            };
            request.onerror = (e) => reject(e.target.error);
        });
    }

    async updateOfflineBadgeCount() {
        const drafts = await this.getAllDrafts();
        const badgeElem = document.getElementById('offline-draft-badge');
        if (badgeElem) {
            badgeElem.textContent = drafts.length;
            badgeElem.style.display = drafts.length > 0 ? 'inline-flex' : 'none';
        }
    }

    registerOnlineListener() {
        window.addEventListener('online', () => {
            console.log("Jaringan online terdeteksi! Memulai otomatisasi sinkronisasi survei...");
            this.syncDraftsToServer();
        });
    }

    async syncDraftsToServer() {
        const drafts = await this.getAllDrafts();
        if (drafts.length === 0) return;

        console.log(`Mengirimkan ${drafts.length} draf survei offline ke server SIPELITA...`);

        for (const draft of drafts) {
            try {
                const formData = new FormData();
                for (const key in draft) {
                    if (key !== 'id' && key !== 'created_at' && key !== 'status') {
                        formData.append(key, draft[key]);
                    }
                }

                const response = await fetch('/input', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                });

                if (response.ok) {
                    await this.deleteDraft(draft.id);
                    console.log(`Draf survei NIK ${draft.nik} berhasil tersinkronisasi!`);
                }
            } catch (err) {
                console.error("Gagal sinkronisasi draf offline:", err);
            }
        }
        
        alert("Sinkronisasi data survei offline ke server SIPELITA berhasil dilakukan!");
    }
}

// Inisialisasi Singleton Instance Client-Side
window.sipelitaOfflineDB = new OfflineSurveyDB();
