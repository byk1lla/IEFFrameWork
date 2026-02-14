@extends('layouts.admin')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
    <div class="titan-card">
        <div
            style="font-size: 0.7rem; color: var(--purple); font-weight: 800; letter-spacing: 2px; text-transform: uppercase;">
            Engine Load</div>
        <div style="font-size: 3rem; font-weight: 900; margin: 10px 0;">0.02ms</div>
        <div style="font-size: 0.75rem; color: var(--text-dim); font-weight: 600;">Optimal performance maintained.</div>
    </div>
    <div class="titan-card">
        <div
            style="font-size: 0.7rem; color: var(--cyan); font-weight: 800; letter-spacing: 2px; text-transform: uppercase;">
            Active Matrix</div>
        <div style="font-size: 3rem; font-weight: 900; margin: 10px 0;">1,240</div>
        <div style="font-size: 0.75rem; color: var(--text-dim); font-weight: 600;">Users connected to nexus.</div>
    </div>
    <div class="titan-card">
        <div
            style="font-size: 0.7rem; color: #f472b6; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;">
            System Integrity</div>
        <div style="font-size: 3rem; font-weight: 900; margin: 10px 0;">99.9%</div>
        <div style="font-size: 0.75rem; color: var(--text-dim); font-weight: 600;">Titanium core stable.</div>
    </div>
</div>

<div class="titan-card" style="margin-top: 40px;">
    <h3 style="font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; margin-bottom: 30px;">Recent
        Activity Logs</h3>
    <div style="display: flex; flex-direction: column; gap: 15px;">
        <div style="padding: 15px; border-left: 3px solid var(--purple); background: rgba(139, 92, 246, 0.02);">
            <span style="color: var(--purple); font-weight: 800;">[SYS]</span> V4 Titanium Obsidian Overhaul Initiated
        </div>
        <div style="padding: 15px; border-left: 3px solid var(--cyan); background: rgba(6, 182, 212, 0.02);">
            <span style="color: var(--cyan); font-weight: 800;">[DOC]</span> Knowledge Base Expanded
        </div>
    </div>
</div>
@endsection