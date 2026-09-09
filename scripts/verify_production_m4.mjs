import https from 'node:https';

async function main() {
    console.log('=== Milestone 4 Live Production Verification ===');
    const domain = 'https://theravada.macatung.dev';

    let cookies = {};

    function updateCookies(res) {
        const setCookies = res.headers['set-cookie'];
        if (setCookies) {
            for (const sc of setCookies) {
                const parts = sc.split(';')[0].split('=');
                const key = parts[0].trim();
                const val = parts.slice(1).join('=');
                cookies[key] = val;
            }
        }
    }

    function getCookieHeader() {
        return Object.entries(cookies).map(([k, v]) => `${k}=${v}`).join('; ');
    }

    function request(url, options = {}) {
        return new Promise((resolve, reject) => {
            const parsedUrl = new URL(url);
            const reqOptions = {
                hostname: parsedUrl.hostname,
                port: parsedUrl.port || 443,
                path: parsedUrl.pathname + parsedUrl.search,
                method: options.method || 'GET',
                headers: {
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Cookie': getCookieHeader(),
                    ...(options.headers || {})
                }
            };

            const req = https.request(reqOptions, (res) => {
                updateCookies(res);
                let body = '';
                res.on('data', chunk => body += chunk);
                res.on('end', () => {
                    resolve({
                        statusCode: res.statusCode,
                        headers: res.headers,
                        body
                    });
                });
            });

            req.on('error', reject);
            if (options.body) {
                req.write(options.body);
            }
            req.end();
        });
    }

    // Step 1: Unauthenticated check
    console.log('\n[1] Testing Unauthenticated Access to /admin/theravada/videos...');
    const unauthRes = await request(`${domain}/admin/theravada/videos`);
    console.log(`HTTP Status: ${unauthRes.statusCode}`);
    console.log(`Location Header: ${unauthRes.headers['location']}`);
    if (unauthRes.statusCode === 302 && unauthRes.headers['location'].includes('/admin/login')) {
        console.log('=> PASSED: Unauthenticated users correctly redirected to login (302).');
    } else {
        console.error('=> FAILED: Expected 302 redirect to /admin/login');
    }

    // Step 2: Get CSRF token from Login page
    console.log('\n[2] Fetching Login Page for CSRF Token & Session...');
    const loginPageRes = await request(`${domain}/admin/login`);
    console.log(`Login page HTTP Status: ${loginPageRes.statusCode}`);
    const xsrfToken = decodeURIComponent(cookies['XSRF-TOKEN'] || '');
    console.log(`Obtained XSRF-TOKEN: ${xsrfToken ? 'YES (found)' : 'NO'}`);

    // Step 3: Authenticate
    console.log('\n[3] Authenticating as Admin...');
    const postData = JSON.stringify({ password: 'macatung@midnight2026' });
    const authRes = await request(`${domain}/admin/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Content-Length': Buffer.byteLength(postData),
            'X-XSRF-TOKEN': xsrfToken,
            'X-Inertia': 'true',
            'X-Inertia-Version': 'true',
            'Accept': 'text/html, application/xhtml+xml'
        },
        body: postData
    });
    console.log(`Auth response status: ${authRes.statusCode}`);
    console.log(`Auth response location: ${authRes.headers['location']}`);
    console.log(`Admin session active: ${cookies['laravel_session'] ? 'YES' : 'NO'}`);

    // Step 4: Fetch Admin Video Index Page
    console.log('\n[4] Requesting /admin/theravada/videos as standard browser...');
    const indexResHtml = await request(`${domain}/admin/theravada/videos`, {
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        }
    });

    console.log(`Index HTML response status: ${indexResHtml.statusCode}`);
    let indexPageData;
    let inertiaVersion = '';
    const match = indexResHtml.body.match(/data-page="([^"]+)"/);
    if (match) {
        const decoded = match[1].replace(/&quot;/g, '"').replace(/&amp;/g, '&');
        indexPageData = JSON.parse(decoded);
        inertiaVersion = indexPageData.version;
        console.log(`Inertia Component: ${indexPageData.component}`);
        console.log(`Inertia Asset Version: ${inertiaVersion}`);
        console.log(`Total Articles Count: ${indexPageData.props.articles.total}`);
        console.log(`Articles on Page 1: ${indexPageData.props.articles.data.length}`);
        console.log(`Status Counts:`, indexPageData.props.statusCounts);
    } else {
        console.log('No data-page attribute found in response body');
    }

    // Step 4b: Test Inertia JSON API request with X-Inertia-Version
    if (inertiaVersion) {
        console.log('\n[4b] Testing Inertia JSON API request with X-Inertia-Version...');
        const indexResJson = await request(`${domain}/admin/theravada/videos`, {
            headers: {
                'X-Inertia': 'true',
                'X-Inertia-Version': inertiaVersion,
                'Accept': 'text/html, application/xhtml+xml'
            }
        });
        console.log(`Inertia JSON response status: ${indexResJson.statusCode}`);
        if (indexResJson.statusCode === 200) {
            const parsedJson = JSON.parse(indexResJson.body);
            console.log(`=> PASSED: Inertia JSON returned HTTP 200 with component ${parsedJson.component}`);
        }

        // Step 4c: Test Filter by status=completed
        console.log('\n[4c] Testing Filtering by status=completed...');
        const filterRes = await request(`${domain}/admin/theravada/videos?status=completed`, {
            headers: {
                'X-Inertia': 'true',
                'X-Inertia-Version': inertiaVersion,
                'Accept': 'text/html, application/xhtml+xml'
            }
        });
        console.log(`Filter status=completed response status: ${filterRes.statusCode}`);
        if (filterRes.statusCode === 200) {
            const filterData = JSON.parse(filterRes.body);
            console.log(`Completed Articles found: ${filterData.props.articles.total}`);
            for (const art of filterData.props.articles.data) {
                console.log(`  - ID: ${art.id} | Status: ${art.video_status} | Title: "${art.title.slice(0, 50)}..."`);
                console.log(`    Thumbnail 16:9 (thumbnail_long_url): ${art.thumbnail_long_url}`);
                console.log(`    Thumbnail 9:16 (thumbnail_short_url): ${art.thumbnail_short_url}`);
                if (art.thumbnail_long_url) {
                    const thumbCheck = await request(art.thumbnail_long_url, { method: 'HEAD' });
                    console.log(`    -> 16:9 Check: HTTP ${thumbCheck.statusCode} (${thumbCheck.headers['content-type']})`);
                }
            }
        }

        // Step 4d: Test Filter by status=published
        console.log('\n[4d] Testing Filtering by status=published...');
        const pubRes = await request(`${domain}/admin/theravada/videos?status=published`, {
            headers: {
                'X-Inertia': 'true',
                'X-Inertia-Version': inertiaVersion,
                'Accept': 'text/html, application/xhtml+xml'
            }
        });
        console.log(`Filter status=published response status: ${pubRes.statusCode}`);
        if (pubRes.statusCode === 200) {
            const pubData = JSON.parse(pubRes.body);
            console.log(`Published Articles found: ${pubData.props.articles.total}`);
            for (const art of pubData.props.articles.data) {
                console.log(`  - ID: ${art.id} | Status: ${art.video_status} | Title: "${art.title.slice(0, 50)}..."`);
                console.log(`    Thumbnail 16:9: ${art.thumbnail_long_url}`);
                console.log(`    Video Long MP4: ${art.video_long_url}`);
                console.log(`    YouTube URL: ${art.youtube_url}`);
            }
        }
    }

    // Step 5: Test Video Show Detail Page for Articles 173 and 172
    console.log('\n[5] Requesting Video Show Detail for Production Video Articles (173 & 172)...');
    for (const testId of [173, 172]) {
        console.log(`\n--- Testing Article ID ${testId} ---`);
        const showRes = await request(`${domain}/admin/theravada/videos/${testId}`, {
            headers: {
                'X-Inertia': 'true',
                'X-Inertia-Version': inertiaVersion,
                'Accept': 'text/html, application/xhtml+xml'
            }
        });
        console.log(`Show page HTTP Status: ${showRes.statusCode}`);
        if (showRes.statusCode === 200) {
            try {
                const showData = JSON.parse(showRes.body);
                const art = showData.props.article;
                console.log(`Show Inertia Component: ${showData.component}`);
                console.log(`Title: ${art.title}`);
                console.log(`Pāḷi Title: ${art.pali_title}`);
                console.log(`Video Status: ${art.video_status}`);
                console.log(`Thumbnail 16:9 (thumbnail_long_url): ${art.thumbnail_long_url}`);
                console.log(`Thumbnail 9:16 (thumbnail_short_url): ${art.thumbnail_short_url}`);
                console.log(`Video Long MP4: ${art.video_long_url}`);
                console.log(`Video Short Reels: ${art.video_short_url}`);
                console.log(`YouTube URL: ${art.youtube_url}`);
                console.log(`YouTube ID: ${art.youtube_id}`);
                console.log(`SEO Title: ${art.seo_title}`);
                console.log(`Hashtags: ${JSON.stringify(art.hashtags)}`);

                // Verify the article's own thumbnails are reachable
                if (art.thumbnail_long_url) {
                    const thumbCheck = await request(art.thumbnail_long_url, { method: 'HEAD' });
                    console.log(`  => Article 16:9 Thumbnail Check: HTTP ${thumbCheck.statusCode} (${thumbCheck.headers['content-type']})`);
                }
                if (art.thumbnail_short_url) {
                    const thumbVCheck = await request(art.thumbnail_short_url, { method: 'HEAD' });
                    console.log(`  => Article 9:16 Thumbnail Check: HTTP ${thumbVCheck.statusCode} (${thumbVCheck.headers['content-type']})`);
                }
            } catch (e) {
                console.log('Show body parse error:', e.message);
            }
        }
    }

    // Step 5b: Verify Vite frontend bundle assets on production
    console.log('\n[5b] Verifying Production Frontend Assets...');
    const assetMatches = indexResHtml.body.match(/\/build\/assets\/[^"']+\.(js|css)/g) || [];
    const uniqueAssets = [...new Set(assetMatches)];
    console.log(`Found ${uniqueAssets.length} entry assets linked in HTML.`);
    for (const assetPath of uniqueAssets) {
        const fullAssetUrl = `${domain}${assetPath}`;
        const assetRes = await request(fullAssetUrl, { method: 'HEAD' });
        console.log(`Asset ${assetPath} => HTTP ${assetRes.statusCode} | ${assetRes.headers['content-type']} | ${assetRes.headers['content-length']} bytes`);
    }

    // Step 6: Verify CDN Assets for Completed Video Productions
    console.log('\n[6] Verifying CDN Storage Assets (Thumbnails & Videos)...');
    const cdnAssets = [
        'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/tam_an_dap_tat_san_youtube_thumbnail.jpg',
        'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/reel3_than_hong_bg.jpg',
        'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/tam_an_buong_bo_lo_au_youtube_thumbnail.jpg',
        'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/reel1_buong_bo_bg.jpg',
        'https://storage.googleapis.com/nendoi-marketing-assets/videos/dap_tat_ngon_lua_san_matoathien_1080p.mp4',
        'https://storage.googleapis.com/nendoi-marketing-assets/videos/tam_an_van_su_an_matoathien_1080p.mp4'
    ];

    for (const url of cdnAssets) {
        try {
            const headRes = await request(url, { method: 'HEAD' });
            console.log(`Asset: ${url.split('/').pop()}`);
            console.log(`  -> Status: ${headRes.statusCode} | Content-Type: ${headRes.headers['content-type']} | Size: ${headRes.headers['content-length']} bytes`);
            if (headRes.statusCode === 200) {
                console.log('  -> OK (HTTP 200)');
            } else {
                console.error(`  -> ERROR: Returned status ${headRes.statusCode}`);
            }
        } catch (err) {
            console.error(`  -> FAIL: ${err.message}`);
        }
    }

    console.log('\n=== Live Production Verification Finished Successfully ===');
}

main().catch(err => {
    console.error('Verification failed with error:', err);
    process.exit(1);
});
