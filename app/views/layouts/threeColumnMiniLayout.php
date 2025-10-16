<?php require APPROOT.'/views/inc/header.php'?>
<style>
    .three-column-layout {
        display: flex;
        height: 100vh;
        overflow: hidden;
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
    }
    
    .template-left {
        width: 275px;
        border-right: 1px solid var(--border);
        overflow: hidden;
    }
    
    .template-center {
        width: 280px;
        border-right: 1px solid var(--border);
        overflow: hidden;
    }
    
    .template-right {
        flex: 1;
        overflow-y: auto;
    }
    
    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .three-column-layout {
            max-width: 100%;
        }
    }
    
    @media (max-width: 992px) {
        .template-right {
            flex: 1;
        }
        
        .three-column-layout {
            flex-wrap: wrap;
        }
        
        .template-left {
            order: 1;
        }
        
        .template-center {
            order: 2;
            width: calc(100% - 275px);
            border-right: none;
        }
        
        .template-right {
            order: 3;
            width: 100%;
            border-top: 1px solid var(--border);
        }
    }
    
    @media (max-width: 768px) {
        .template-left {
            width: 70px;
        }
        
        .template-center {
            width: calc(100% - 70px);
        }
    }
    
    /* Scrollbar styling */
    .three-column-layout ::-webkit-scrollbar {
        width: 6px;
    }
    
    .three-column-layout ::-webkit-scrollbar-track {
        background: var(--bg);
    }
    
    .three-column-layout ::-webkit-scrollbar-thumb {
        background: rgba(158, 212, 220, 0.3);
        border-radius: 10px;
    }
    
    .three-column-layout ::-webkit-scrollbar-thumb:hover {
        background: var(--link);
    }
</style>
<?php echo $styles;?>
<div class="three-column-layout">
    <div class="template-left">
        <?php echo $leftsidebar; ?>
    </div>
    <div class="template-center">
        <?php echo $center_content; ?>
    </div>
    <div class="template-right">
        <?php echo $rightsidebar; ?>
    </div>
</div>
<script>
    <?php echo $scripts; ?>
</script>