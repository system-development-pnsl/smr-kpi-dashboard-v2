import $ from 'jquery'
import axios from 'axios'
import Chart from 'chart.js/auto'

window.$ = window.jQuery = $

// ── Axios defaults ────────────────────────────────────────────────────────────
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content

window.axios = axios
window.Chart = Chart

$(function () {

    // ── Sidebar ───────────────────────────────────────────────────────────────
    const $sidebar   = $('#sidebar')
    const $backdrop  = $('#sidebar-backdrop')
    const isMobile   = () => window.innerWidth < 768

    // On mobile always start closed; on desktop restore saved state
    let collapsed = isMobile() ? true : localStorage.getItem('sidebar_collapsed') === 'true'

    function expandSidebarContent() {
        $sidebar.removeClass('w-[60px]').addClass('w-[220px]')
        $('#sidebar-logo').removeClass('justify-center px-0').addClass('px-4 gap-2.5')
        $('[data-sidebar-link]').removeClass('justify-center h-9 px-0').addClass('h-9 px-3')
        $('#sidebar-notif-link').removeClass('justify-center h-9 px-0 w-full').addClass('h-9 px-3 w-full')
        $('.sidebar-expanded').show()
        $('.sidebar-collapsed').hide()
    }

    function collapseSidebarContent() {
        $sidebar.removeClass('w-[220px]').addClass('w-[60px]')
        $('#sidebar-logo').removeClass('px-4 gap-2.5').addClass('justify-center px-0')
        $('[data-sidebar-link]').removeClass('h-9 px-3').addClass('justify-center h-9 px-0')
        $('#sidebar-notif-link').removeClass('h-9 px-3 w-full').addClass('justify-center h-9 px-0 w-full')
        $('.sidebar-expanded').hide()
        $('.sidebar-collapsed').show()
    }

    function applySidebar() {
        if (isMobile()) {
            // Mobile: use inline style so CSS class order doesn't interfere
            $sidebar[0].style.transform = collapsed ? 'translateX(-100%)' : 'translateX(0)'
            $backdrop.toggleClass('hidden', collapsed)
            expandSidebarContent()
        } else {
            // Desktop: clear inline transform and let md:translate-x-0 take over
            $sidebar[0].style.transform = ''
            $backdrop.addClass('hidden')
            collapsed ? collapseSidebarContent() : expandSidebarContent()
        }
    }

    applySidebar()

    $('#sidebar-toggle').on('click', function () {
        collapsed = !collapsed
        if (!isMobile()) localStorage.setItem('sidebar_collapsed', collapsed)
        applySidebar()
    })

    // Close drawer when tapping backdrop (mobile)
    $backdrop.on('click', function () {
        collapsed = true
        applySidebar()
    })

    // Re-apply on resize (handles mobile ↔ desktop switch)
    $(window).on('resize', function () {
        collapsed = isMobile() ? true : localStorage.getItem('sidebar_collapsed') === 'true'
        applySidebar()
    })

    // ── Flash message auto-hide ───────────────────────────────────────────────
    const $flash = $('#flash-success')
    if ($flash.length) {
        setTimeout(() => $flash.fadeOut(300), 4000)
    }

    // ── Task view toggle ──────────────────────────────────────────────────────
    const $listView   = $('#task-view-list')
    const $kanbanView = $('#task-view-kanban')
    const $btnList    = $('#btn-view-list')
    const $btnKanban  = $('#btn-view-kanban')

    if ($listView.length) {
        function setView(view) {
            if (view === 'list') {
                $listView.show()
                $kanbanView.hide()
                $btnList.addClass('bg-brand-surface shadow-card text-brand-black').removeClass('text-brand-muted')
                $btnKanban.removeClass('bg-brand-surface shadow-card text-brand-black').addClass('text-brand-muted')
            } else {
                $kanbanView.show()
                $listView.hide()
                $btnKanban.addClass('bg-brand-surface shadow-card text-brand-black').removeClass('text-brand-muted')
                $btnList.removeClass('bg-brand-surface shadow-card text-brand-black').addClass('text-brand-muted')
            }
        }

        $btnList.on('click', () => setView('list'))
        $btnKanban.on('click', () => setView('kanban'))
        setView('list')
    }

    // ── Documents: select-all checkbox ───────────────────────────────────────
    $('#select-all-checkbox').on('change', function () {
        $('input[name="confirmed_fields[]"]').prop('checked', this.checked)
    })

    // ── Select-search (single) ────────────────────────────────────────────────
    $(document).on('click', '[data-ss-trigger]', function (e) {
        e.stopPropagation()
        const $wrap  = $(this).closest('[data-ss-wrap]')
        const isOpen = $wrap.hasClass('ss-open')
        closeAllDropdowns()
        if (!isOpen) {
            $wrap.addClass('ss-open')
            $wrap.find('[data-ss-dropdown]').show()
            $wrap.find('[data-ss-chevron]').addClass('rotate-180')
            $wrap.find('[data-ss-search]').val('').trigger('input').focus()
        }
    })

    $(document).on('click', '[data-ss-option]', function (e) {
        e.stopPropagation()
        const $wrap      = $(this).closest('[data-ss-wrap]')
        const val        = String($(this).data('value'))
        const label      = $(this).data('label')
        const formId     = $wrap.data('form-id')
        const $trigger   = $wrap.find('[data-ss-trigger]')

        $wrap.find('[data-ss-input]').val(val)
        $wrap.find('[data-ss-label]').text(label)
        $trigger.removeClass('text-brand-subtle').addClass('text-brand-black')
        $wrap.find('[data-ss-option]').removeClass('font-semibold bg-brand-bg').find('[data-ss-check]').hide()
        $(this).addClass('font-semibold bg-brand-bg').find('[data-ss-check]').show()

        closeAllDropdowns()
        if (formId) setTimeout(() => document.getElementById(formId).submit(), 0)
    })

    $(document).on('click', '[data-ss-clear]', function (e) {
        e.stopPropagation()
        const $wrap      = $(this).closest('[data-ss-wrap]')
        const formId     = $wrap.data('form-id')
        const placeholder = $wrap.data('placeholder')
        const $trigger   = $wrap.find('[data-ss-trigger]')

        $wrap.find('[data-ss-input]').val('')
        $wrap.find('[data-ss-label]').text(placeholder)
        $trigger.addClass('text-brand-subtle').removeClass('text-brand-black')
        $wrap.find('[data-ss-option]').removeClass('font-semibold bg-brand-bg').find('[data-ss-check]').hide()

        closeAllDropdowns()
        if (formId) setTimeout(() => document.getElementById(formId).submit(), 0)
    })

    $(document).on('input', '[data-ss-search]', function () {
        const q     = $(this).val().toLowerCase()
        const $wrap = $(this).closest('[data-ss-wrap]')
        let visible = 0
        $wrap.find('[data-ss-option]').each(function () {
            const matches = !q || String($(this).data('label')).toLowerCase().includes(q)
            $(this).closest('li').toggle(matches)
            if (matches) visible++
        })
        $wrap.find('[data-ss-noresults]').toggle(visible === 0)
    })

    // ── Select-search-multi ───────────────────────────────────────────────────
    $(document).on('click', '[data-ssm-trigger]', function (e) {
        e.stopPropagation()
        const $wrap  = $(this).closest('[data-ssm-wrap]')
        const isOpen = $wrap.hasClass('ssm-open')
        closeAllDropdowns()
        if (!isOpen) {
            $wrap.addClass('ssm-open')
            $wrap.find('[data-ssm-dropdown]').show()
            $wrap.find('[data-ssm-chevron]').addClass('rotate-180')
            $wrap.find('[data-ssm-search]').val('').trigger('input').focus()
        }
    })

    $(document).on('click', '[data-ssm-option]', function (e) {
        e.stopPropagation()
        const $wrap     = $(this).closest('[data-ssm-wrap]')
        const val       = String($(this).data('value'))
        const fieldName = $wrap.data('field-name')
        const $existing = $wrap.find(`[data-ssm-inputs] input[value="${CSS.escape(val)}"]`)

        if ($existing.length) {
            $existing.remove()
            $(this).find('[data-ssm-cbwrap]').removeClass('bg-brand-black border-brand-black').addClass('border-brand-border bg-white')
            $(this).find('[data-ssm-checksvg]').hide()
        } else {
            $wrap.find('[data-ssm-inputs] [data-ssm-fallback]').remove()
            $wrap.find('[data-ssm-inputs]').append(`<input type="hidden" name="${fieldName}" value="${val}" data-ssm-hidden>`)
            $(this).find('[data-ssm-cbwrap]').addClass('bg-brand-black border-brand-black').removeClass('border-brand-border bg-white')
            $(this).find('[data-ssm-checksvg]').show()
        }

        updateSsmLabel($wrap)
    })

    $(document).on('click', '[data-ssm-clearall]', function (e) {
        e.stopPropagation()
        const $wrap     = $(this).closest('[data-ssm-wrap]')
        const fieldName = $wrap.data('field-name')
        $wrap.find('[data-ssm-option]').each(function () {
            $(this).find('[data-ssm-cbwrap]').removeClass('bg-brand-black border-brand-black').addClass('border-brand-border bg-white')
            $(this).find('[data-ssm-checksvg]').hide()
        })
        $wrap.find('[data-ssm-inputs]').html(`<input type="hidden" name="${fieldName}" value="" data-ssm-fallback>`)
        updateSsmLabel($wrap)
    })

    $(document).on('click', '[data-ssm-done]', function (e) {
        e.stopPropagation()
        closeAllDropdowns()
    })

    $(document).on('input', '[data-ssm-search]', function () {
        const q     = $(this).val().toLowerCase()
        const $wrap = $(this).closest('[data-ssm-wrap]')
        let visible = 0
        $wrap.find('[data-ssm-option]').each(function () {
            const matches = !q || String($(this).data('label')).toLowerCase().includes(q)
            $(this).closest('li').toggle(matches)
            if (matches) visible++
        })
        $wrap.find('[data-ssm-noresults]').toggle(visible === 0)
    })

    function updateSsmLabel($wrap) {
        const $selected  = $wrap.find('[data-ssm-inputs] [data-ssm-hidden]')
        const count      = $selected.length
        const placeholder = $wrap.data('placeholder')
        const $trigger   = $wrap.find('[data-ssm-trigger]')

        if (count === 0) {
            $wrap.find('[data-ssm-label]').text(placeholder)
            $trigger.addClass('text-brand-subtle').removeClass('text-brand-black')
            $wrap.find('[data-ssm-count]').hide()
            $wrap.find('[data-ssm-footer]').hide()
            $wrap.find('[data-ssm-clearall]').hide()
        } else {
            const vals   = $selected.map(function () { return $(this).val() }).get()
            const labels = []
            $wrap.find('[data-ssm-option]').each(function () {
                if (vals.includes(String($(this).data('value')))) labels.push($(this).data('label'))
            })
            const labelText = labels.length === 1 ? labels[0] : labels[0] + ' +' + (labels.length - 1)
            $wrap.find('[data-ssm-label]').text(labelText)
            $trigger.removeClass('text-brand-subtle').addClass('text-brand-black')
            $wrap.find('[data-ssm-count]').text(count).show()
            $wrap.find('[data-ssm-footer]').show()
            $wrap.find('[data-ssm-footercount]').text(count + ' selected')
            $wrap.find('[data-ssm-clearall]').show()
        }
    }

    // ── Close all dropdowns on outside click / Escape ─────────────────────────
    function closeAllDropdowns() {
        $('[data-ss-wrap].ss-open').removeClass('ss-open').find('[data-ss-dropdown]').hide()
        $('[data-ss-wrap] [data-ss-chevron]').removeClass('rotate-180')
        $('[data-ssm-wrap].ssm-open').removeClass('ssm-open').find('[data-ssm-dropdown]').hide()
        $('[data-ssm-wrap] [data-ssm-chevron]').removeClass('rotate-180')
        $('.dp-calendar').hide()
        $('.dp-trigger').removeClass('dp-open')
    }

    $(document).on('click', closeAllDropdowns)
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeAllDropdowns()
    })

    // ── Custom Date Picker ────────────────────────────────────────────────────
    const MONTH_NAMES = ['January','February','March','April','May','June',
                         'July','August','September','October','November','December']
    const SHORT_MONTHS = ['Jan','Feb','Mar','Apr','May','Jun',
                          'Jul','Aug','Sep','Oct','Nov','Dec']

    function dpFormatDisplay(iso) {
        if (!iso) return null
        const d = new Date(iso + 'T00:00:00')
        return `${String(d.getDate()).padStart(2,'0')} ${SHORT_MONTHS[d.getMonth()]} ${d.getFullYear()}`
    }

    function dpBuildGrid($wrap, year, month) {
        const todayStr    = new Date().toISOString().slice(0, 10)
        const selectedIso = $wrap.data('dp-val') || ''
        const firstDow    = new Date(year, month, 1).getDay()
        const daysInMonth = new Date(year, month + 1, 0).getDate()
        const prevDays    = new Date(year, month, 0).getDate()

        let html = '<div class="dp-grid">'
        // Day-of-week headers
        for (const d of ['Su','Mo','Tu','We','Th','Fr','Sa'])
            html += `<div class="dp-dow">${d}</div>`

        // Leading grey cells (previous month)
        for (let i = firstDow - 1; i >= 0; i--)
            html += `<div class="dp-day dp-other">${prevDays - i}</div>`

        // Current month days
        for (let d = 1; d <= daysInMonth; d++) {
            const iso = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`
            let cls = 'dp-day'
            if (iso === todayStr)    cls += ' dp-today'
            if (iso === selectedIso) cls += ' dp-selected'
            html += `<div class="${cls}" data-date="${iso}">${d}</div>`
        }

        // Trailing grey cells (fill last row)
        const totalCells = Math.ceil((firstDow + daysInMonth) / 7) * 7
        const trailing   = totalCells - firstDow - daysInMonth
        for (let d = 1; d <= trailing; d++)
            html += `<div class="dp-day dp-other">${d}</div>`

        html += '</div>'
        return html
    }

    function dpRender($wrap) {
        const year  = $wrap.data('dp-year')
        const month = $wrap.data('dp-month')
        const $cal  = $wrap.find('.dp-calendar')
        $cal.find('.dp-month-label').text(`${MONTH_NAMES[month]} ${year}`)
        $cal.find('.dp-grid-wrap').html(dpBuildGrid($wrap, year, month))
    }

    $('input[type="date"].input').each(function () {
        const $orig = $(this)
        if ($orig.data('dp-init')) return
        $orig.data('dp-init', true)

        const initVal  = $orig.val()
        const required = $orig.prop('required')
        const name     = $orig.attr('name')
        const errClass = $orig.hasClass('border-status-red') ? 'border-status-red' : ''

        // Replace with hidden input
        const $hidden = $(`<input type="hidden" name="${name}" value="${initVal}">`)
        $orig.replaceWith($hidden)

        // Build trigger + calendar
        const displayText = initVal ? dpFormatDisplay(initVal) : ''
        const $wrap = $(`
            <div class="dp-wrap">
                <button type="button" class="dp-trigger input ${errClass}">
                    <span class="dp-display">${displayText || '<span class=\'dp-ph\'>Select date…</span>'}</span>
                    <svg class="dp-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </button>
                <div class="dp-calendar" style="display:none">
                    <div class="dp-cal-header">
                        <button type="button" class="dp-prev">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <span class="dp-month-label"></span>
                        <button type="button" class="dp-next">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                    <div class="dp-grid-wrap"></div>
                    <div class="dp-cal-footer">
                        ${!required ? '<button type="button" class="dp-clear-btn">Clear</button>' : '<span></span>'}
                        <button type="button" class="dp-today-btn">Today</button>
                    </div>
                </div>
            </div>
        `)

        $hidden.after($wrap)
        $wrap.data('dp-val', initVal)

        const base  = initVal ? new Date(initVal + 'T00:00:00') : new Date()
        $wrap.data('dp-year',  base.getFullYear())
        $wrap.data('dp-month', base.getMonth())
        dpRender($wrap)

        // Open / close — use fixed positioning so overflow-hidden parents don't clip
        $wrap.find('.dp-trigger').on('click', function (e) {
            e.stopPropagation()
            const isOpen = $wrap.find('.dp-calendar').is(':visible')
            closeAllDropdowns()
            if (!isOpen) {
                const $cal = $wrap.find('.dp-calendar')
                const rect = this.getBoundingClientRect()
                $cal.css({ top: rect.bottom + 6, left: rect.left })
                $cal.show()
                $wrap.find('.dp-trigger').addClass('dp-open')
            }
        })

        // Prev / Next month
        $wrap.on('click', '.dp-prev', function (e) {
            e.stopPropagation()
            let m = $wrap.data('dp-month') - 1
            let y = $wrap.data('dp-year')
            if (m < 0) { m = 11; y-- }
            $wrap.data('dp-month', m).data('dp-year', y)
            dpRender($wrap)
        })
        $wrap.on('click', '.dp-next', function (e) {
            e.stopPropagation()
            let m = $wrap.data('dp-month') + 1
            let y = $wrap.data('dp-year')
            if (m > 11) { m = 0; y++ }
            $wrap.data('dp-month', m).data('dp-year', y)
            dpRender($wrap)
        })

        // Select day
        $wrap.on('click', '.dp-day:not(.dp-other)', function (e) {
            e.stopPropagation()
            const iso = $(this).data('date')
            $hidden.val(iso)
            $wrap.data('dp-val', iso)
            $wrap.find('.dp-display').html(dpFormatDisplay(iso))
            dpRender($wrap)
            closeAllDropdowns()
        })

        // Today
        $wrap.on('click', '.dp-today-btn', function (e) {
            e.stopPropagation()
            const today = new Date()
            const iso   = today.toISOString().slice(0, 10)
            $hidden.val(iso)
            $wrap.data('dp-val', iso)
            $wrap.data('dp-year', today.getFullYear())
            $wrap.data('dp-month', today.getMonth())
            $wrap.find('.dp-display').html(dpFormatDisplay(iso))
            dpRender($wrap)
            closeAllDropdowns()
        })

        // Clear
        $wrap.on('click', '.dp-clear-btn', function (e) {
            e.stopPropagation()
            $hidden.val('')
            $wrap.data('dp-val', '')
            $wrap.find('.dp-display').html('<span class="dp-ph">Select date…</span>')
            dpRender($wrap)
            closeAllDropdowns()
        })

        $wrap.on('click', '.dp-calendar', function (e) { e.stopPropagation() })
    })

})

// ── KPI chart initializer ─────────────────────────────────────────────────────
window.initSparkline = (canvasId, data, status) => {
    const colors = {
        green: '#16A34A',
        amber: '#D97706',
        red:   '#DC2626',
    }
    const color = colors[status] || colors.green
    const ctx   = document.getElementById(canvasId)
    if (!ctx) return

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map((_, i) => i),
            datasets: [{
                data,
                borderColor:     color,
                borderWidth:     1.5,
                backgroundColor: color + '18',
                fill:            true,
                tension:         0.4,
                pointRadius:     0,
            }],
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales:  { x: { display: false }, y: { display: false } },
            animation: { duration: 600 },
        },
    })
}

// ── Revenue bar chart ─────────────────────────────────────────────────────────
window.initRevenueChart = (canvasId, labels, datasets) => {
    const ctx = document.getElementById(canvasId)
    if (!ctx) return

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: datasets.map((ds, i) => ({
                ...ds,
                backgroundColor: ['#0A0A0A', '#737373', '#A3A3A3', '#D4D4D4'][i],
                borderRadius:    i === datasets.length - 1 ? { topLeft: 3, topRight: 3 } : 0,
                stack:           'a',
            })),
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { font: { family: 'DM Sans', size: 11 }, boxWidth: 8 } },
            },
            scales: {
                x: { grid: { display: false },  ticks: { font: { family: 'DM Sans', size: 10 } } },
                y: { grid: { color: '#E5E5E3' }, ticks: {
                    font: { family: 'DM Sans', size: 10 },
                    callback: v => '$' + (v / 1000) + 'K',
                }},
            },
        },
    })
}
