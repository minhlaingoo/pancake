@php
    $id = 'chart-' . Str::random(10);
@endphp

<div class="h-80">
    <div id="{{ $id }}" class="w-full h-full"></div>
</div>

@script
<script>
    (function () {
        const labels = @json($labels);
        const datasets = @json($dataset);
        const container = document.getElementById("{{ $id }}");
        if (!container || !window.d3) return;

        const margin = { top: 20, right: 20, bottom: 50, left: 50 };
        const width = container.clientWidth - margin.left - margin.right;
        const height = container.clientHeight - margin.top - margin.bottom;

        const svg = d3.select(container).append('svg')
            .attr('width', '100%')
            .attr('height', '100%')
            .attr('viewBox', `0 0 ${container.clientWidth} ${container.clientHeight}`)
            .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        const x = d3.scaleBand().domain(labels).range([0, width]).padding(0.3);
        const allValues = datasets.flatMap(ds => ds.data);
        const y = d3.scaleLinear().domain([0, d3.max(allValues) * 1.1]).range([height, 0]);

        svg.append('g').attr('transform', `translate(0,${height})`).call(d3.axisBottom(x));
        svg.append('g').call(d3.axisLeft(y));

        // X axis label
        svg.append('text')
            .attr('x', width / 2).attr('y', height + 40)
            .attr('text-anchor', 'middle').attr('class', 'text-xs fill-current text-muted-foreground')
            .text('Chamber');

        // Y axis label
        svg.append('text')
            .attr('transform', 'rotate(-90)').attr('x', -height / 2).attr('y', -40)
            .attr('text-anchor', 'middle').attr('class', 'text-xs fill-current text-muted-foreground')
            .text('Volume (ml)');

        datasets.forEach(ds => {
            const color = ds.backgroundColor || ds.borderColor;
            svg.selectAll(`.bar-${ds.label.replace(/\s/g, '')}`)
                .data(ds.data)
                .join('rect')
                .attr('x', (d, i) => x(labels[i]))
                .attr('y', d => y(d))
                .attr('width', x.bandwidth())
                .attr('height', d => height - y(d))
                .attr('fill', color)
                .attr('rx', 3);
        });

        // Legend
        const legend = svg.append('g').attr('transform', `translate(0, ${height + 25})`);
        datasets.forEach((ds, i) => {
            const color = ds.backgroundColor || ds.borderColor;
            const g = legend.append('g').attr('transform', `translate(${i * 180}, 0)`);
            g.append('rect').attr('width', 12).attr('height', 12).attr('fill', color);
            g.append('text').attr('x', 16).attr('y', 10).attr('class', 'text-xs fill-current').text(ds.label);
        });
    })();
</script>
@endscript
